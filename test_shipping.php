<?php

class WC_Fast_Delivery_Shipping_Method extends WC_Shipping_Method{

          public function __construct(){
                $this->id = 'fast_delivery_shipping_method';
                  $this->method_title = __( 'Fast Delivery Shipping Method', 'woocommerce' );

                  // Load the settings.
                  $this->init_form_fields();
                  $this->init_settings();


                  // Define user set variables
                  $this->enabled        = $this->get_option( 'enabled' );
                  $this->title                 = $this->get_option( 'title' );


                  add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
          }

          public function init_form_fields(){
                  $this->form_fields = array(
                    'enabled' => array(
                      'title'                 => __( 'Enable/Disable', 'woocommerce' ),
                      'type'                         => 'checkbox',
                      'label'                 => __( 'Enable Fast Delivery Shipping', 'woocommerce' ),
                      'default'                 => 'yes'
                    ),
                    'title' => array(
                      'title'                 => __( 'Method Tittle', 'woocommerce' ),
                      'type'                         => 'text',
                      'description'         => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                      'default'                => __( 'Fast Delivery Shipping', 'woocommerce' ),
                    )
                );
          }
          public function is_available( $package ){
                  foreach ( $package['contents'] as $item_id => $values ) {
              $_product = $values['data'];
              $weight =        $_product->get_weight();
              if($weight > 10){
                      return false;
              }
                  }
                  return true;
          }
          public function calculate_shipping($package){

                  //get the total weight and dimensions

            $weight = 0;
            $dimensions = 0;
            foreach ( $package['contents'] as $item_id => $values ) {
              $_product  = $values['data'];
              $weight =        $weight + $_product->get_weight() * $values['quantity'];
              $dimensions = $dimensions + (($_product->length * $values['quantity']) * $_product->width * $_product->height);

            }
            //calculate the cost according to the table
            switch ($weight) {
                case ($weight < 1):
                  switch ($dimensions) {
                    case ($dimensions <= 1000):
                    $cost = 3;
                    break;
                    case ($dimensions > 1000):
                    $cost = 4;
                    break;
                  }
                 break;
                case ($weight >= 1 && $weight < 3 ):
                  switch ($dimensions) {
                    case ($dimensions <= 3000):
                    $cost = 10;
                    break;
                  }
                break;
                case ($weight >= 3 && $weight < 10):
                  switch ($dimensions) {
                    case ($dimensions <= 5000):
                    $cost = 25;
                    break;
                    case ($dimensions > 5000):
                    $cost = 50;
                    break;
                  }
                 break;
              }
            // send the final rate to the user.
           $c= $this->add_rate( array(
              'id'         => $this->id,
              'label' => $this->title,
              'cost'         => $cost
            ));
          }
}
