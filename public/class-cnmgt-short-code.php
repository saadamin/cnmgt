<?php
// The shortcode function
function contact_management() {
    $html="<div id='cnmgt_main_all' class='cnmgt'>
    <div class='container'>
        <div class='row'>
            <h1>All People</h1>";
            $people = Cnmgt_Admin::cnmgt_get_people();
            if($people){
                $html.="<table id='people' class='display' style='width:100%'>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>";
                    foreach($people as $person){
                        $html.="<tr>
                            <td>$person->name</td>
                            <td>$person->email</td>
                            <td>$person->phone_numbers</td>
                        </tr>";
                        }

                    $html.="</tbody>
                    <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                        </tr>
                    </tfoot>
                </table>";
        }else{
                $html.="<span class='alert alert-warning'>No people found</span>";
            }
        $html.="</div>
    </div>
</div>";
  
// Output needs to be return
return $html;
        }
    // Register shortcode
    add_shortcode('contact_management', 'contact_management'); 