<?php
namespace App\Base;

class GTShipping extends \WC_Shipping_Method {

    //the name of the shipping zone cookie to save on
    // the user computer
    const COOKIE_NAME = "gt_zone";

    /**
    * Constructor for your shipping class
    *
    * @access public
    * @return void
    */
       public function __construct() {
           $this->id          = 'gt_shipping';
           $this->title       = __( 'Point de livraison' );
           $this->method_description = __( 'Solution de livraison' ); //
           $this->enabled     = "yes"; // This can be added as an setting but for this example its forced enabled
           $this->init();
       }

       /**
        * Init your settings
        *
        * @access public
        * @return void
        */
       function init() {
           // Load the settings API
           $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
           $this->init_settings(); // This is part of the settings API. Loads settings you previously init.

           // Save settings in admin if you have any defined
           add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
       }

       /**
        * calculate_shipping function.
        *
        * @access public
        * @param mixed $package
        * @return void
        */
       public function calculate_shipping( $package = array() )
       {
           //lets set the shipping cost first
           $calculatorInstance = new \App\Base\CalculateShipping;
           $calculatorInstance->set_zone_cost();

           $cost = $_SESSION[\App\Base\CalculateShipping::SHIPPING_COST];

           if($cost > 0)
           {

               $this->title = "Point de livraison (". $_SESSION[\App\Base\CalculateShipping::SHIPPING_DESTINATION] .")";
           }

           $rate = array(
						'id' => $this->id,
						'label' => $this->title,
						'cost' => $cost,
						'calc_tax' => 'per_item'
					);

			// Register the rate
			$this->add_rate( $rate );
       }

}
?>
