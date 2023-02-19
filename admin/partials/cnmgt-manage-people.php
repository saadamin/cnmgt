<?php
// echo '<pre>';print_r($_POST);echo '</pre>';
$cnmgt_name = $cnmgt_email=$phone_number=$country_code=$warning=$url='';
$button_class='new';
if(isset($_POST['cnmgt_submitButton'])){ //check if form was submitted
    if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'add_edit_person' ) ) {
        wp_die( 'Are you cheating?' );
    } else {
        $button_class=$_POST['cnmgt_submitButton'];
        $warning= '<ul id="form_warnings">';
        if(strlen(trim($_POST['cnmgt_name'])) < 5) {$warning .= '<li>Name must be at least 3 characters long.</li>';}else{$cnmgt_name = $_POST['cnmgt_name'];}
        if(!filter_var(trim($_POST['cnmgt_email']), FILTER_VALIDATE_EMAIL) ) {$warning .= '<li>Please write valid email.</li>';}else{$cnmgt_email = $_POST['cnmgt_email'];}
        $phoneNumbers=check_all_phone_numbers($_POST,$warning);
        if(!$phoneNumbers){$warning .= '<li>Please choose country code and write 9 digit phone numbers.</li>';}
        if('<ul id="form_warnings">' == $warning){
            global $wpdb;
            if($_POST['cnmgt_submitButton'] == 'new'){
                $existingEmail = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cnmgt WHERE email = %s" , trim($_POST['cnmgt_email'])));
                $existingPhone = check_existing_phone($phoneNumbers);
                if($existingEmail || $existingPhone){
                    $warning .= $existingEmail ? '<li>Person with this email already exists.</li>' : "<li>Person with phone number $existingPhone already exists.</li>";
                }else{
                    $people =$wpdb->insert( 
                        $wpdb->prefix.'cnmgt',
                        array(
                            'name' => trim($_POST['cnmgt_name']),
                            'email' => trim($_POST['cnmgt_email']),
                            'phone_numbers' =>escape_array($phoneNumbers),
                        )
                    );
                    $cnmgt_name = $cnmgt_email='';
                    $warning = "Success! ".$_POST['cnmgt_name'].' has been added to the database.';
                }
            }elseif($_POST['cnmgt_submitButton'] == 'edit'){
                $phoneNumbers=check_all_phone_numbers($_POST,$warning);
                $existingEmail = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cnmgt WHERE email = %s AND id != %d" , array(trim($_POST['cnmgt_email']),$_POST['cnmgt_id'])));
                $existingPhone = check_existing_phone($phoneNumbers,$_POST['cnmgt_id']);
                $B=$wpdb->last_query;
                if($existingEmail || $existingPhone){
                    $warning .= $existingEmail ? '<li>Person with this email already exists.</li>' : "<li>Person with phone number $existingPhone already exists.</li>";
                }else{
                    $wpdb->update(
                        $wpdb->prefix.'cnmgt',
                        array(
                            'name' => trim($_POST['cnmgt_name']),
                            'email' => trim($_POST['cnmgt_email']),
                            'phone_numbers' => escape_array($phoneNumbers),
                        ),
                        array('id' => $_POST['cnmgt_id'])
                    );
                    $button_class='new';
                    $cnmgt_name = $cnmgt_email=$phone_number=$country_code='';$url=substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'&action=edit'));
                    $warning = "<li class='alert alert-success'>Success! ".$_POST['cnmgt_name'].' has been updated in the database.</li>';
                }
            }
        }
        $warning.='</ul>';
    }
}else{
    if(isset($_GET['action']) && $_GET['action'] =='edit' && isset($_GET['id']) && intval($_GET['id']) > 0){
        global $wpdb;
        $people = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cnmgt WHERE id = %d AND  deleted = %d" , array(intval($_GET['id']), 0)));
        if($people){
            $cnmgt_name = $people[0]->name;
            $cnmgt_email = $people[0]->email;
            $phoneNumbers = explode(',',str_replace(array( '[', ']' ), '', $people[0]->phone_numbers));
            $button_class = 'edit';
        }
    }
}
function check_existing_phone($numbers,$id=0){
    global $wpdb;
    foreach($numbers as $number){
        if($id){
            $result= $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cnmgt WHERE phone_numbers Like (%s) AND id != %d" , array("%$number%",$_POST['cnmgt_id'])));
        }else{
            $result=$wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cnmgt WHERE phone_numbers Like (%s)" , array("%$number%")));
        }
        $r=$wpdb->last_query;
        if($result){
            return $number;
        }
    }
    return false;
}
function check_all_phone_numbers($post,$warning){
    foreach($post['cnmgt_phone_number'] as $key => $value){
        $country_code = preg_replace('/\D/', '', $post['cnmgt_country_code'][$key]);
        $phone_number = preg_replace('/\D/', '', $post['cnmgt_phone_number'][$key]);
        if (strlen($phone_number) !== 9 || strlen($country_code) === 0) { // I could check $country_code by callingCodes key from $countries object but I have less time.
            return false;
        }
        $phoneNumbers[]= $country_code.$phone_number;
    }
    return $phoneNumbers;
}
function escape_array($arr){
    return '['.implode(',', $arr).']';
}
function get_country_code_only($full_number)
{
    return substr($full_number ,0, strlen($full_number)-9);
}
function get_phone_number_only($full_number){
    return substr($full_number, strlen($full_number)-9, strlen($full_number));
}
?>
<div id="cnmgt_main" class="cnmgt">
<div class="container">
  <form method="post" action ="<?php echo $url; ?>">
    <?php wp_nonce_field( 'add_edit_person' ); 
    if($button_class=='edit'){
        echo '<input type="hidden" name="cnmgt_id" value="'.$_GET['id'].'">';
    }
    ?>
    <div class="row">
        <?php if('<ul id="form_warnings"></ul>' !== $warning){ echo $warning; } ?>
      <h4><?php echo $button_class=='edit' ? 'Edit a person' : 'Add a person'; ?></h4>
      <div class="input-group input-group-icon">
        <input type="text" name="cnmgt_name" placeholder="Full Name" required value="<?php echo $cnmgt_name; ?>"/>
        <div class="input-icon"><i class="fa fa-user"></i></div>
      </div>
      <div class="input-group input-group-icon">
        <input type="email" name="cnmgt_email" placeholder="Email Address" required value="<?php echo $cnmgt_email; ?>"/>
        <div class="input-icon"><i class="fa fa-envelope"></i></div>
      </div>
      <div class="input-group input-group-icon">
        <?php if($button_class=='edit'){
            foreach($phoneNumbers as $index => $phoneNumber){
                $country_code = get_country_code_only($phoneNumber);
                $phone_number = get_phone_number_only($phoneNumber);    
            ?>
                <div id="country_div<?php echo $index+1; ?>" class="clonedInput" align="center">
                    <select class="countries">
                        <option value=''>Select a country</option>
                        <?php foreach(json_decode($countries) as $country){ 
                            $callingCodes=$country->callingCodes[0];
                            $selected = $country_code == $callingCodes ? 'selected' : '';
                            echo "<option class='country_code_$callingCodes' value='$callingCodes' $selected>$country->name ($callingCodes)</option>";
                        }
                        ?>
                    </select>
                    <input type="number" required class="phone_number" name="cnmgt_phone_number[]" placeholder="Write phone number" value="<?php echo $phone_number; ?>" />
                    <button class="delete_number" id="btnDel<?php echo $index+1; ?>" del="<?php echo $index+1; ?>" type="button">Delete</button>
                </div>
            <?php }
        }else{ ?>
            <div id="country_div1" class="clonedInput" align="center">
                <select class="countries">
                    <option value=''>Select a country</option>
                    <?php foreach(json_decode($countries) as $country){ 
                        $callingCodes=$country->callingCodes[0];
                        echo "<option class='country_code_$callingCodes' value='$callingCodes'>$country->name ($callingCodes)</option>";
                    }
                    ?>
                </select>
                <input type="number" required class="phone_number" name="cnmgt_phone_number[]" placeholder="Write phone number" value="<?php echo $phone_number; ?>" />
            </div>
        <?php } ?>
      </div>
        <button id="btnAdd" type="button">Add More</button>
        
      <button class="btn" name="cnmgt_submitButton" value="<?php echo $button_class; ?>" type="submit">Save</button>
    </div>
  </form>
</div>
</div>

<script>
jQuery(document).ready(function() {
    syncSelects();
	
    jQuery('#btnAdd').click(function () {
        var num = jQuery('.clonedInput').length;
        newNum = new Number(num + 1);
        newElem = jQuery('#country_div' + num).clone().attr('id', 'country_div' + newNum).fadeIn('normal'); 
        // Store the block in a variable
        var jQueryblock = jQuery('.clonedInput:last');
        // Grab the selected value
        var theValue = jQueryblock.find(':selected').val();
        // Clone the block 
        var clone = jQueryblock.clone();
        clone.find('span.select2-container').remove();
        clone.find('input.phone_number').val("");
        clone.find('button.delete_number').attr('disabled', false);
        // Grab the select in the clone
        select = clone.find('select');select.val("");
        var newId="country_div"+newNum;
        console.log(newId);
        // Update its ID by concatenating theValue to the current ID
        jQuery(select).parent().attr('id', newId);
        jQuery('#country_div' + num).after(clone);
        syncSelects();
    });

    jQuery('.delete_number').click(function () {
        if( jQuery('.clonedInput').length == 1 ) {
            alert("You can't delete the last one!");
            jQuery(this).attr('disabled', true);
            return false;
        }else{
            var id = jQuery(this).attr('del');
            jQuery('#country_div' + id).remove();
        }
    });
    b=0;
    jQuery(document.body).on("change",".countries",function(){
        syncSelects(jQuery(this).select2("data")[0].id);
    });
    
    function syncSelects(val = null) {
        let selected = [];
        jQuery('.cnmgt_country_code').remove();
        jQuery('select.countries').each(function() {
            // let value = val !== null ? val : this.value;
            let value = this.value;
            if(value.length > 0){
                selected.push(value);
            }
            document.querySelector("form").insertAdjacentHTML("afterbegin", '<input class="cnmgt_country_code" type="hidden" name="cnmgt_country_code[]" value="'+value+'" />');
            this.childNodes.forEach(function(el){
                jQuery(el).removeProp('disabled'); jQuery(el).removeAttr('disabled');
            });
            
        });
        selected.forEach(element => {
            jQuery(".country_code_"+element).prop('disabled', !jQuery(".country_code_"+element).prop('disabled'));
        });
        jQuery('select').select2();
    }
    jQuery(document).on('keyup',".phone_number", function() {
        if(this.value.length > 9){ this.value = this.value.slice(0, 9);}
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});
</script>