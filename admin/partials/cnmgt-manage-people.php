<?php
print_r($countries);
$cnmgt_name = $cnmgt_email= '';
if(isset($_POST['cnmgt_submitButton'])){ //check if form was submitted
    $cnmgt_name =isset($_POST['cnmgt_name']) ? $_POST['cnmgt_name'] : '';
  $message = "Success! ".$_POST['cnmgt_name'].' has been added to the database.';
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
      <button class="btn" name="cnmgt_submitButton" type="submit">Save</button>
    </div>
  </form>
</div>