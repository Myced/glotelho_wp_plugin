<?php
namespace App\Base;

class FixCategoryEditProblem
{

    public function register()
    {
        add_action('admin_head', [$this, 'register_css']);
    }

    public function register_css()
    {
        ?>
        <style media="screen">
            .wp-admin #wpfooter
            {
                margin: 50px;
            }

            #wpfooter p
            {
                display: none;
            }
        </style>
        <?php
    }
}


 ?>
