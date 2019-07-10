<?php
/**
* Ced Plugin class
**/
namespace App;


class GTShippingPlugin
{
    public $plugin_name = PLUGIN_NAME;


    public static function instantiate($class)
    {
        return new $class;
    }

    public static function get_services()
    {
        return [
            \App\Base\InitSession::class,
            \App\Admin\Admin::class,
            \App\Base\Sellers::class,
            \App\Base\Enqueue::class,
            \App\Base\SettingsLink::class,
            \App\Base\OrderMetaBox::class,
            \App\Base\ZoneController::class,
            \App\Base\TownController::class,
            \App\Base\RegionController::class,
            \App\Base\QuarterController::class,

            //now control front end scripts
            \App\Base\EnqueueFrontStyles::class,
            \App\Base\Checkout::class,
            \App\Ajax\CheckoutAjax::class,
            \App\Base\CalculateShipping::class,
            // \App\Base\MomoController::class
            //clasess now for reports.
        ];
    }


    public static function register_services()
    {
        foreach(self::get_services() as $class)
        {
            $instance = self::instantiate($class); //make an instance of the class

            if(method_exists($instance, 'register'))
            {
                $instance->register();
            }
        }
    }

    public function uninstall()
    {
        return;
    }


}
 ?>
