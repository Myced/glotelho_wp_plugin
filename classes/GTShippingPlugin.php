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
            \App\Base\EnqueueScript::class,
            \App\Base\SettingsLink::class,
            \App\Base\OrderMetaBox::class,
            \App\Base\ZoneController::class,
            \App\Base\TownController::class,
            \App\Base\RegionController::class,
            \App\Base\QuarterController::class,
            \App\Base\AddProductCostPrice::class,
            \App\Base\AfterOrderPlaced::class,
            \App\Base\OrderCostMetabox::class,
            \App\Base\SaveFrontOrderSeller::class,

            \App\Base\AdvancePaymentMetaBox::class,
            \App\Base\SavePaymentDate::class,
            \App\Base\PaymentMethodBox::class,

            //now control front end scripts
            \App\Base\EnqueueFrontStyles::class,
            \App\Base\Checkout::class,
            \App\Ajax\CheckoutAjax::class,
            \App\Base\CalculateShipping::class,
            // \App\Base\MomoController::class
            //clasess now for reports.

            //now add classes for google analytics
            \App\Google\GAAnalytics::class,
            \App\Google\PurchaseEvent::class,
            \App\Google\ViewItemEvent::class,
            // \App\Google\AddToCartEvent::class,
            // \App\Google\RemoveFromCartEvent::class,
            \App\Google\BeginCheckoutEvent::class,
            \App\Google\CheckoutStepsEvent::class,


            //Fix Edit category problem on site
            \App\Base\FixCategoryEditProblem::class,

            //override wp smart search style
            \App\Base\OverrideSearchModal::class,
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
