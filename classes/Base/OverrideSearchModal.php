<?php
namespace App\Base;

class OverrideSearchModal
{

    public function register()
    {
        add_action('admin_head', [$this, 'load_override']);
    }

    public function load_override()
    {
        echo '<style>
            .media-modal-backdrop {
                display: none;
            }
        </style>';
    }
}


 ?>
