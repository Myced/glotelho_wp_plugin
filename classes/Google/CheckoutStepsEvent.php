<?php
namespace App\Google;


class CheckoutStepsEvent
{
    public function register()
    {
        add_action('woocommerce_thankyou', [$this, 'send_checkout_steps'], 8);
    }

    public function send_checkout_steps($order_id)
    {
        $order = wc_get_order($order_id);

        $first_name = $order->get_billing_first_name();
        $last_name = $order->get_billing_last_name();

        $name = $first_name . ' ' . $last_name;

        $steps = [];

        //client name step
        $step = [
            "checkout_step" => 1,
            "checkout_option" => 'Client Name',
            "value" => $name
        ];

        array_push($steps, $step);

        //now step 2 Client Number
        $step = [
            "checkout_step" => 2,
            "checkout_option" => 'Client Telephone',
            "value" => $order->get_billing_phone()
        ];

        array_push($steps, $step);

        //step 3 shipping method
        $step = [
            "checkout_step" => 3,
            "checkout_option" => 'Shipping Method',
            "value" => $order->get_shipping_method()
        ];

        array_push($steps, $step);

        //step 4 Payment Method
        $step = [
            "checkout_step" => 4,
            "checkout_option" => 'Payment Method',
            "value" => $order->get_payment_method_title()
        ];

        array_push($steps, $step);

        $this->send_analytics($steps);
    }

    public function send_analytics($steps)
    {

        ?>
        <script type="text/javascript">
            <?php foreach ($steps as $step): ?>
                gtag('event', 'set_checkout_option', {
                    "checkout_step": <?php echo $step['checkout_step']; ?>,
                    "checkout_option": "<?php echo $step['checkout_option']; ?>",
                    "value": "<?php echo $step['value']; ?>"
                });
            <?php endforeach; ?>
        </script>

        <?php
    }

}

 ?>
