<?php
namespace App\Base;

class InitSession
{

    public function register()
    {
        add_action('init', [$this, 'myStartSession'], 1);
        add_action('wp_logout', [$this, 'myEndSession']);
        add_action('wp_login', [$this, 'myEndSession']);
    }

    public function myStartSession()
    {
        if(!session_id()) {
            session_start();
        }
    }

    public function myEndSession()
    {
        session_destroy ();
    }
}

?>
