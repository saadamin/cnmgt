<?php
echo '<pre>';print_r($_POST);echo '</pre>';
$cnmgt_name = $cnmgt_email=$message=$warning='';
if(isset($_POST['cnmgt_submitButton']) && isset($_POST['cnmgt_name']) && isset($_POST['cnmgt_email']) && isset($_POST['cnmgt_country_code']) && isset($_POST['cnmgt_phone_number'])){ //check if form was submitted correctly
        $warning= '<ul id="form_warnings">';
        if(strlen($_POST['cnmgt_name']) < 5) {$warning .= '<li>Name must be at least 3 characters long.</li>';}
        if(!filter_var($_POST['cnmgt_email'], FILTER_VALIDATE_EMAIL) ) {$warning .= '<li>Please write valid email.</li>';}
        if(!check_all_phone_numbers($_POST)){$warning .= '<li>Please choose country code and phone number correctly.</li>';}
        $warning.='</ul>';
        global $wpdb;
    if($_POST['cnmgt_submitButton'] == 'new'){
        $existing = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cnmgt_people WHERE cnmgt_email = '{$_POST['cnmgt_email']}'");
        if($existing){
            $warning .= '<li>Person with this email already exists.</li>';
        }else{
            $wpdb->insert(
                $wpdb->prefix.'cnmgt_people',
                array(
                    'cnmgt_name' => $_POST['cnmgt_name'],
                    'cnmgt_email' => $_POST['cnmgt_email'],
                    'cnmgt_country_code' => $_POST['cnmgt_country_code'],
                    'cnmgt_phone_number' => $_POST['cnmgt_phone_number'],
                )
            );
            $message = "Success! ".$_POST['cnmgt_name'].' has been added to the database.';
        }

    }
    // $cnmgt_name =isset($_POST['cnmgt_name']) ? $_POST['cnmgt_name'] : '';
    // $message = "Success! ".$_POST['cnmgt_name'].' has been added to the database.';
}
function check_all_phone_numbers($post){
    foreach($post['cnmgt_phone_number'] as $key => $value){
        $country_code = preg_replace('/\D/', '', $post['cnmgt_country_code'][$key]);
        $phone_number = preg_replace('/\D/', '', $post['cnmgt_phone_number'][$key]);
        if (!strlen($phone_number) == 9 || !strlen($country_code) >  1) { // I could check $country_code by callingCodes key from $countries object but I have less time.
            return false;
        }
    }
}
?>
<div class="container">
  <form method="post" action ="">
    <div class="row">
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
            echo "<option value='$callingCodes'>$country->name ($callingCodes)</option>";
        }
        ?>
        </select>
        <input type="number" class="phone_number" name="cnmgt_phone_number[]" placeholder="Write phone number"/>
      </div>
      <button class="btn" name="cnmgt_submitButton" value="new" type="submit">Save</button>
    </div>
  </form>
</div>

<script>
    // In your Javascript (external .js resource or <script> tag)
jQuery(document).ready(function() {
    jQuery('.countries').select2();
});
</script>