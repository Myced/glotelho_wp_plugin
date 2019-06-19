<?php
namespace App\Base;


class EnqueueFrontStyles
{

    public function register()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
    }

    public function enqueue_styles()
    {
        //enque magnific popup



        if(! $this->is_enqueued_script("magnific-popup"))
        {
            // wp_enqueue_script(PLUGIN_NAME, GT_ASSETS_URL . 'jquery.magnific-popup.min.js', array('jquery'), '', true );
        }
        else {

        }
        // wp_enqueue_style(PLUGIN_NAME, GT_ASSETS_URL . 'magnific-popup.css');

        wp_enqueue_script(PLUGIN_NAME, GT_ASSETS_URL . 'popup_init.js', array('jquery'), '', true );
        wp_enqueue_style(PLUGIN_NAME,GT_ASSETS_URL . 'popup_style.css');

    }

    public function is_enqueued_script( $script )
    {
        return isset( $GLOBALS['wp_scripts']->registered[ $script ] );
    }

}

?>
