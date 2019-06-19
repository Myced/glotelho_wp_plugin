<?php
namespace App\Base;

class MomoController
{

    public function register()
    {
        add_action('plugins_loaded', [$this, 'initialise_momo']);

        add_filter('woocommerce_payment_gateways', [$this, 'add_momo_payment'] );
    }

    public function initialise_momo()
    {
        // return require_once BASE_DIRECTORY . '/classes/Base/NewMomo.php';
    }

    public function add_momo_payment($methods)
    {
        $methods[] = "\App\Base\NewMomo";

        return $methods;
    }
}

?>
