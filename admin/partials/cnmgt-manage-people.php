<?php
// echo '<pre>';print_r($_SERVER);echo '</pre>';
$cnmgt_name = $cnmgt_email=$phone_number=$country_code=$warning=$url='';
$button_class='new';
if(isset($_POST['cnmgt_submitButton'])){ //check if form was submitted
    if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'add_edit_person' ) ) {
        wp_die( 'Are you cheating?' );
    } else {
        $warning= '<ul id="form_warnings">';
        if(strlen(trim($_POST['cnmgt_name'])) < 5) {$warning .= '<li>Name must be at least 3 characters long.</li>';}else{$cnmgt_name = $_POST['cnmgt_name'];}
        if(!filter_var(trim($_POST['cnmgt_email']), FILTER_VALIDATE_EMAIL) ) {$warning .= '<li>Please write valid email.</li>';}else{$cnmgt_email = $_POST['cnmgt_email'];}
        $phoneNumbers=check_all_phone_numbers($_POST,$warning);
        if(!$phoneNumbers){$warning .= '<li>Please choose country code and write 9 digit phone numbers.</li>';}
        if('<ul id="form_warnings">' == $warning){
            global $wpdb;
            if($_POST['cnmgt_submitButton'] == 'new'){
                $existingEmail = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cnmgt WHERE email = %s" , trim($_POST['cnmgt_email'])));
                $existingPhone = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cnmgt WHERE phone_numbers IN (%s)" , array(escape_array($phoneNumbers))));
                if($existingEmail || $existingPhone){
                    $warning .= $existingEmail ? '<li>Person with this email already exists.</li>' : '<li>Person with this phone number already exists.</li>';
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
                $wpdb->update(
                    $wpdb->prefix.'cnmgt',
                    array(
                        'name' => trim($_POST['cnmgt_name']),
                        'email' => trim($_POST['cnmgt_email']),
                        'phone_numbers' => escape_array($phoneNumbers),
                    ),
                    array('id' => $_POST['cnmgt_id'])
                );
                $cnmgt_name = $cnmgt_email=$phone_number=$country_code='';$url=substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'&action=edit'));
                $warning = "<li class='alert alert-success'>Success! ".$_POST['cnmgt_name'].' has been updated in the database.</li>';
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
            $phoneNumbers = explode(',',$people[0]->phone_numbers);
            $country_code = get_country_code_only($phoneNumbers[0]);
            $phone_number = get_phone_number_only($phoneNumbers[0]);
            $button_class = 'edit';
        }
    }
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
    return implode(',', $arr);
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
      <h4>Account</h4>
      <div class="input-group input-group-icon">
        <input type="text" name="cnmgt_name" placeholder="Full Name" value="<?php echo $cnmgt_name; ?>"/>
        <div class="input-icon"><i class="fa fa-user"></i></div>
      </div>
      <div class="input-group input-group-icon">
        <input type="email" name="cnmgt_email" placeholder="Email Address" value="<?php echo $cnmgt_email; ?>"/>
        <div class="input-icon"><i class="fa fa-envelope"></i></div>
      </div>
      <div class="input-group input-group-icon">
      <select class="countries" name="cnmgt_country_code[]">
      <option value=''>Select a country</option>"
        <?php foreach(json_decode($countries) as $country){ 
            $callingCodes=$country->callingCodes[0];
            $selected = $country_code == $callingCodes ? 'selected' : '';
            echo "<option value='$callingCodes' $selected>$country->name ($callingCodes)</option>";
        }
        ?>
        </select>
        <input type="number" class="phone_number" name="cnmgt_phone_number[]" placeholder="Write phone number" value="<?php echo $phone_number; ?>" />
      </div>
      <button class="btn" name="cnmgt_submitButton" value="<?php echo $button_class; ?>" type="submit">Save</button>
    </div>
  </form>
</div>
</div>

<script>
jQuery(document).ready(function() {
    jQuery('.countries').select2();
});
</script>