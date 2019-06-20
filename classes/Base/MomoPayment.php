<?php
class NewMomo extends \WC_Payment_Gateway {

    private $momo_email;
    private $momo_number;

    public function __construct() {

        $this->id				= 'gt_momo_payment';
        $this->method_title     = __( 'Mobile Money Payment', 'woocommerce' );
        $this->icon 			= GT_ASSETS_URL . 'images/momo_icon.jpeg';
        $this->method_description = 'Take User Payments through MTN Mobile money.';
        $this->has_fields 		= true;

        $this->init_form_fields();
        $this->init_settings();

        $this->title 			= "MTN Mobile Money";
        $this->description      = "Pay for your order through MTN Mobile Money in Cameroon";

        $this->momo_email   = $this->settings['momo_email'];
        $this->momo_number       = $this->settings['momo_number'];

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

        add_action('woocommerce_thankyou_'.$this->id, array( $this, 'thankyou_page' ) );
        add_action('woocommerce_receipt_'.$this->id, array( $this, 'receipt_page' ));

    }

    function init_form_fields() {

        $this->form_fields = array(
            'enabled' => array(
                'title' => __( 'Enable/Disable', 'woocommerce' ),
                'type' => 'checkbox',
                'label' => __( 'Enable MomoPayment Payment', 'woocommerce' ),
                'default' => 'yes'
            ),
            'momo_email' => array(
                            'title' => __( 'Mobile Money Email', 'woocommerce' ),
                            'type' => 'text',
                            'label' => __( 'Mobile Money Email', 'woocommerce' ),
                            'default' => 'tncedric@yahoo.com'
                        ),
            'momo_number' => array(
                            'title' => __( 'Telephone Number', 'woocommerce' ),
                            'type' => 'text',
                            'label' => __("Momo Account Tel Number", 'woocommerce'),
                            'default' => __( '673901939', 'woocommerce' )
            )

        );
    }

    /**
     * Admin Panel Options
     * - Options for bits like 'title' and availability on a country-by-country basis
    **/
    public function admin_options() {

        echo '<h3>'.__('MTN Mobile Money Settings', 'woocommerce').'</h3>';
        echo '<table class="form-table">';

        $this->generate_settings_html();
        echo '</table>';
    }

    /**
     * There are no payment fields for TRANZCORE, but we want to show the description if set.
    **/
    function payment_fields() {
        if ($this->description) echo wpautop(wptexturize($this->description));
        ?>
        <ol class="my-list">
            <li>Fill your inforation requested above.</li>
            <li>Then Enter Your MTN mobile money number</li>
            <li>A message enter your phone asking you to dail *126# and enter your pin.</li>
            <li>Dial *126# and enter your pin to confirm your payment</li>
            <li>If the payment goes through, the your order will be placed.</li>
        </ol>

        <label for="">Enter Your MTN Mobile Money number</label>

        <input type="text" name="gt_user_momo_number" value=""
            style="background-color: #fff; border-radius: 0px; color: #222;"required class="form-controll"
            placeholder="Enter your phone number">
        <?php
    }

    public function validate_fields()
    {
        if(isset($_POST['gt_user_momo_number']))
        {
            if(!empty($_POST['gt_user_momo_number']))
            {
                return $this->is_valid_tel($_POST['gt_user_momo_number']);
            }
            else {
                $this->place_error("Your Phone number is required");
                return false;
            }
        }

        //the
        $this->place_error("The Phone Number is required");
        return false;
    }

    private function is_valid_tel($number)
    {
        $tel = $this->filterTel($number);

        if(strlen($tel) !== 9)
        {
            $this->place_error("Telephone Number must be 9 digits");
            return false;
        }
        else {
            //check that it contians only numbers
            if(!is_numeric($tel))
            {
                $this->place_error("Telephone number must contain only digits");
                return false;
            }
        }

        return true;
    }

    private function place_error($error)
    {
        wc_add_notice($error, "error");
    }

    private function filterTel($number)
    {
        $regex = '/[\s\,\.\-\+\_]/';
        if(preg_match($regex, $number))
        {
            $filter = preg_filter($regex, '', $number);
        }
        else
        {
            $filter = $number;
        }

        return $filter;
    }

    /**
     * Receipt Page
    **/
    function receipt_page( $order ) {

        echo '<p>'.__('Thank you for your order, You will be contacted soon', 'woocommerce').'</p>';

    }

    /**
     * Process the payment and return the result
    **/
    public function process_payment( $order_id ) {

        $order = new WC_Order( $order_id );

        //grab the phone number
        $tel = $this->filterTel($_POST['gt_user_momo_number']);

        $amount = $order->get_total();

        // Mark as on-hold (we're awaiting the cheque)
        $order->update_status('on-hold', __( 'Awaiting MTN Mobile Money payment', 'woocommerce' ));

        //since the telephone has passed validation, make a mobile money payment
        $momoProcessor = new \App\Momo\MomoProcessor($tel, $amount, $this->momo_email);

        //process the mobile money payment.
        $response = $momoProcessor->pay();


        if($response == false)
        {
            $this->place_error("Sorry we are having trouble processing payments at this time");
            return $this->send_error_response();
        }
        else {

            //parse the response and send result/
            $parser = new \App\Momo\MomoParser($response);
            $parser->parse();

            //now check if the request was Successful
            if($parser->success == true)
            {
                //log the result
                $user_id = get_current_user_id();
                $user_name  = $_POST['billing_first_name'] . ' ' . $_POST['billing_last_name'];

                //log the result to the database
                $parser->logPayout($order->get_id(), $user_id, $user_name, $this->momo_email);

                $this->complete_order($order);
                return $this->send_success_response($order);
            }
            else {
                //send an error message
                $this->place_error($parser->message);
                return $this->send_error_response();
            }
        }


    }

    private function complete_order($order)
    {
        global $woocommerce;

        $message = "Payment Received via MTN Mobile Money";
        $order->add_order_note( __($message, 'woothemes') );

        // Reduce stock levels
        $order->reduce_order_stock();

        // Remove cart
        $woocommerce->cart->empty_cart();

        //let woocommerce take care of clearing cart and updating order status.
        $order->payment_complete();
    }

    private function send_error_response()
    {
        return array(
            'result' 	=> 'error',
            //'redirect'	=> add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
            'redirect' => ""
        );
    }

    private function send_success_response($order)
    {
        return array(
            'result' 	=> 'success',
            //'redirect'	=> add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
            'redirect' => $order->get_checkout_order_received_url()
        );
    }


    function thankyou_page() {
        ?>
        <p>
            Thanks your MTN Mobile Money payment has been received.
            <br>
            Your order is being processed and you will be contacted soon.
        </p>
        <?php

    }


}

 ?>
