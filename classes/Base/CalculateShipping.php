<?php
namespace App\Base;

use App\Base\GTShipping;

class CalculateShipping
{

    const SHIPPING_COST = "SHIPPING_COST";
    const SHIPPING_DESTINATION = "SHIPPING_DESTINATION";
    const SHIPPING_COST_MESSAGE = "SHIPPING_COST_MESSAGE";

    public $shipping_method = "gt_shipping";

    public $order_zone = "order_zone";

    public function register()
    {
        add_action('woocommerce_shipping_init', [$this, 'init_shipping_method'] );

        add_filter('woocommerce_shipping_methods', [$this, 'addShippingMethod']);
        add_action("woocommerce_cart_calculate_fees", [$this, 'set_zone_cost']);

        //show the zone location after the shipping total
        add_action('woocommerce_after_shipping_calculator', [$this, 'show_shipping_details']);
        add_action('woocommerce_review_order_after_shipping', [$this, 'show_shipping_details']);

        add_action( 'woocommerce_checkout_update_order_meta', [$this, 'save_extra_checkout_fields'], 10, 2 );

        //display the zone id on the admin page
        add_action( 'woocommerce_admin_order_data_after_billing_address', [$this, 'show_zone_in_admin_page_billing'], 10, 1 );

    }

    public function show_shipping_details()
    {
        if($this->shipping_method == $this->get_shipping_method())
        {
            if(is_checkout())
            {
                ?>
                <tr class="cart-subtotal">
        			<th>
                        Zone:- &nbsp; &nbsp; &nbsp; <?php echo $_SESSION[self::SHIPPING_DESTINATION]; ?>
                    </th>
        			<td>
                        <span class="woocommerce-Price-amount amount">
                            <?php
                            if($_SESSION[self::SHIPPING_COST] > 0)
                            {
                                echo $_SESSION[self::SHIPPING_COST_MESSAGE];
                                ?>
                                <button type="button" name="button"
                                    class="button btn-primary btn-flat mini_popup">
                                    Changer votre zone
                                </button>
                                <?php
                            }
                            else {
                                ?>
                                <button type="button" name="button"
                                    class="button alt btn-flat mini_popup">
                                    Choisir la Zone
                                </button>
                                <?php
                            }
                             ?>
                        </span>
                    </td>
                </tr>
                <?php
            }

        }

        else
        {
            ?>
            <tr class="cart-subtotal">
    			<th>
                    <?php echo "Livraison:" . $this->get_selected_shipping_method(); ?>
                </th>
    			<td>
                    <span class="woocommerce-Price-amount amount">
                        <?php echo number_format($this->get_selected_shipping_method_cost()); ?> FCFA
                    </span>
                </td>
            </tr>
            <?php
        }

    }

    private function get_selected_shipping_method_cost()
    {
        $rate_table = array();

        $shipping_methods = WC()->shipping->get_shipping_methods();

        foreach($shipping_methods as $shipping_method){
            $shipping_method->init();

            foreach($shipping_method->rates as $key=>$val)
                $rate_table[$key] = $val->cost;
        }

        return $rate_table[WC()->session->get( 'chosen_shipping_methods' )[0]];
    }

    private function get_selected_shipping_method()
    {
        $rate_table = array();

        $shipping_methods = WC()->shipping->get_shipping_methods();

        foreach($shipping_methods as $shipping_method){
            $shipping_method->init();

            foreach($shipping_method->rates as $key=>$val)
                $rate_table[$key] = $val->label;
        }

        return $rate_table[WC()->session->get( 'chosen_shipping_methods' )[0]];
    }

    public function save_extra_checkout_fields($order_id)
    {
        if ( ! empty( $_POST['gt_zone'] ) ) {

            $invalid_zones = ['', '0', '-01', '00', '-1'];

            if(! in_array($_POST['gt_zone'], $invalid_zones))
            {
                update_post_meta( $order_id, $this->order_zone, sanitize_text_field( $_POST['gt_zone']) );
            }
        }
    }

    function show_zone_in_admin_page_billing($order)
    {
        $zone_id = get_post_meta( $order->get_id(), $this->order_zone, true );

        if($zone_id != "")
        {
            $zone = $this->get_zone($zone_id);
            $price = $this->get_shipping_cost($zone_id);

            echo '<p><strong>'.__('Livraison')
                    . ':</strong> '
                    . $zone->post_title
                    . ' ('
                    . number_format($price)
                    . ' FCFA)'
                    . '</p>';
            echo '<br>';
        }
        else {
            echo '<p><strong>'.__('Livraison')
                    . ':</strong> '
                    . "Retrait en Magasin"
                    . '</p>';
        };
    }

    private function get_shipping_method()
    {
        return WC()->session->get('chosen_shipping_methods')[0];
    }

    public function init_shipping_method()
    {
        // $shipping = new GTShipping;
        // $shipping->init();
        // return $shipping;
    }

    public function set_zone_cost()
    {
        if(isset($_POST))
        {
            //if a valid region has been selected, then
            //we update the cost for shipping to that zone.
            $invalid_zones = ['', '0', '-01', '00', '-1'];

            if(isset($_POST['post_data']))
            {
                $selected_zone = $this->get_selected_zone($_POST['post_data']);
            }
            elseif(isset($_POST['gt_zone'])) {
                $selected_zone = $_POST['gt_zone'];
            }
            else {
                $selected_zone = "-01";
            }

            // echo $selected_zone; die();
            if(! in_array($selected_zone, $invalid_zones))
            {
                //it is not an invalid zone
                //so ge the zone details.
                $zone = $this->get_zone($selected_zone); //no need for zone details here.

                $shipping_cost = $this->get_shipping_cost($selected_zone);

                //set the session for this shipping cost
                $_SESSION[self::SHIPPING_COST] = $shipping_cost;

                //message displayed to user
                $_SESSION[self::SHIPPING_DESTINATION] = $zone->post_title;

                $_SESSION[self::SHIPPING_COST_MESSAGE] = "Coût/Cost: " . number_format($shipping_cost) . ' FCFA';
            }

            else {
                //set the session for this shipping cost
                $_SESSION[self::SHIPPING_COST] = 0;

                //message displayed to user
                if(is_checkout())
                {
                    $_SESSION[self::SHIPPING_DESTINATION] = "Sélectionner votre point de livraison";
                }
                else {
                    $_SESSION[self::SHIPPING_DESTINATION] = "";
                }
                $_SESSION[self::SHIPPING_COST_MESSAGE] = "Coût/Cost: " . '--' . ' FCFA';
            }

        }
    }

    private function get_shipping_cost($zone_id)
    {
        $data = $this->get_zone_data($zone_id);

        return $data['price'];
    }

    private function get_zone_data($zone_id)
    {
        return get_post_meta( $zone_id, '_gt_plugin_zone_key', true );
    }

    private function get_zone($zone_id)
    {
        return get_post($zone_id);
    }

    private function get_selected_zone($post_data)
    {
        $data = explode('&', $post_data);
        $invalid_zones = ['', '0', '-01', '00', '-1'];

        foreach($data as $set)
        {
            if (strpos($set, 'gt_zone') !== false) {
                $zone_set = $set;

                $zone_array = explode('=', $zone_set);

                //save the zone as a cookie;
                $cookie_name = GTShipping::COOKIE_NAME;
                $cookie_value = $zone_array[1];

                //set the expiry time to 1 year
                $expiry = time() + (86400 * 365); //= 1day x 365 days :- 1 year.

                //only set the cookie if the zone is a valid one

                if(! in_array($cookie_value, $invalid_zones))
                {
                    setcookie($cookie_name, $cookie_value, $expiry, "/");
                }


                return $zone_array[1]; //correspond to the value.
            }
        }

        return '';
    }

    public function addShippingMethod($methods)
    {
        $methods[$this->shipping_method] = "\App\Base\GTShipping";

		return $methods;
    }
}

?>
