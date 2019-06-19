<?php
/**
* Plugin Activation class
**/
namespace App\Base;

/**
 *
 */
class Enqueue
{

    public function register()
    {
        //we don't need admin scripts for now.
        // add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
    }

    public function enqueue_styles()
    {
        wp_enqueue_script(PLUGIN_NAME, GT_ASSETS_URL . 'admin/script.js' );
        wp_enqueue_style(PLUGIN_NAME, GT_ASSETS_URL . 'admin/style.css' );
    }
}

 ?>
