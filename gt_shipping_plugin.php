<?php
/**
* @package GT Plugin
**/

/**
Plugin Name: Glotelho Shipping Extension
Plugin URI: https://glotelho.cm
Description: This is a plugin to setup custom user checkout by zone and calculate prices based on zones
Version: 2.1.0
Author: Equip Technique Glotelho
Author URI: https://glotelho.cm
Licence: GPLv2 or later
Text Domain: GT Plugin.
Domain Path: /languages
**/

//forbid direct script access
if( ! defined('ABSPATH'))
{
    die("Direct Script Access is forbiden");
}

//include composer autoload
require_once(__DIR__ . '/vendor/autoload.php');

//do my definitions here
define('GT_PLUGIN_URL', plugins_url() . '/gt_shipping_plugin/');
define('GT_ASSETS_URL', GT_PLUGIN_URL . 'assets/');
define('GT_CLASSES_URL',  'classes/');
define('PLUGIN_NAME', 'gt_plugin');
define('PLUGIN_BASENAME', plugin_basename(__FILE__));
define("BASE_DIRECTORY", __DIR__);


//include the core class file

use App\GTShippingPlugin;

register_activation_hook(__FILE__, ['\App\Base\Activate', 'activate']);
register_deactivation_hook(__FILE__, ['\App\Base\Deactivate', 'deactivate']);

if(class_exists('App\GTShippingPlugin'))
{
    //register the plugin services
    GTShippingPlugin::register_services();

}

else {
    die("Class does not exist");
}

add_action('plugins_loaded', 'init_payment');

//intialise mobile money payment here
function init_payment()
{
    require_once BASE_DIRECTORY . '/classes/Base/MomoPayment.php';
    //end of class declaration
}

/**
* Add the MTN Mobile Money payment gateway to WooCommerce
**/
function woocommerce_add_momo_gateway($methods) {
    $methods[] = 'NewMomo';
    return $methods;
}

add_filter('woocommerce_payment_gateways', 'woocommerce_add_momo_gateway' );

 ?>
