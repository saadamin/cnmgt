<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.saadamin.com
 * @since      1.0.0
 *
 * @package    Cnmgt
 * @subpackage Cnmgt/admin/partials
 */
// echo '<pre>';print_r($_SERVER);echo '</pre>';
$message='';
if(isset($_POST['action']) && $_POST['action'] == 'delete'){
    if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'delete_id_'.$_POST['id'] ) ) {
        print 'Sorry, your nonce did not verify.';
        exit;
    } else {
        $delete=Cnmgt_Admin::cnmgt_delete_person($_POST['id']);
        if($delete){
            $message= '<div class="alert alert-success">Person deleted successfully.</div>';
        }else{
            $message= '<div class="alert alert-danger">Person not deleted.</div>';
        }
    }
}
?>

<div id="cnmgt_main_all" class="cnmgt">
    <div class="container">
        <div class="row">
            <h1>All People</h1>
            <?php echo $message; ?>
            <?php $people = Cnmgt_Admin::cnmgt_get_people();
            if($people){ ?>
                <table id="people" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($people as $person){  ?>
                        <tr>
                            <td><?php echo $person->name; ?></td>
                            <td><?php echo $person->email; ?></td>
                            <td><?php echo $person->phone_numbers; ?></td>
                            <td>
                                <form id="delete_person<?php echo $person->id; ?>" class="delete_person" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                    <?php wp_nonce_field( 'delete_id_'.$person->id ); ?>
                                    <input type="hidden" name="id" value="<?php echo $person->id; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="button" class="btn delete_button" person_id="<?php echo $person->id; ?>" value="Delete">Delete</button>
                                </form>
                                <a class="btn" href='<?php echo str_replace("?page=cnmgt","?page=cnmgt_manage_users",$_SERVER['REQUEST_URI'])."&action=edit&id=$person->id"; ?>'>Edit</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                </table>
        <?php }else{
                echo '<span class="alert alert-warning">No people found</span>';
            } ?>
        </div>
    </div>
</div>
<script>
    jQuery(function() {
    jQuery(".delete_button").click(function(){
        if (confirm("Are you sure you want to delete?")){
            jQuery('form#delete_person'+jQuery(this).attr('person_id')).submit();
        }
    });
    });
    jQuery(document).ready(function () {
    // Setup - add a text input to each footer cell
    jQuery('#people tfoot th').each(function () {
        var title = jQuery(this).text();
        if(title != 'Action'){
            jQuery(this).html('<input type="text" placeholder="Search ' + title + '" />');
        }
    });
 
    // DataTable
    var table = jQuery('#people').DataTable({
        initComplete: function () {
            // Apply the search
            this.api()
                .columns()
                .every(function () {
                    var that = this;
                    jQuery('input', this.footer()).on('keyup change clear', function () {
                        if (that.search() !== this.value) {
                            that.search(this.value).draw();
                        }
                    });
                });
        },
    });
});
</script>