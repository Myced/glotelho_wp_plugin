<?php
namespace App\Base;

class OrderCostMetabox
{

    public function register()
    {
        add_action( 'add_meta_boxes', [$this, 'add_cost_box'] );
        add_action( 'save_post', [$this, 'save_cost_meta_box']);
    }

    public function add_cost_box()
    {
        global $post;
        $box_id = "gt_order_cost";
        $title = "Order Cost Prices";
        $callback = [$this, 'show_meta_box'];
        $screen = "shop_order";
        $context = "normal";
        $callback_args = null;
        $priority = "high";

        if($post->post_type == "shop_order")
        {
            add_meta_box(
                $box_id, $title, $callback, $screen,
                $context, $priority,$callback_args
            );
        }

    }

    public function show_meta_box()
    {
        global $post;
        $order = wc_get_order($post->ID);

        wp_nonce_field( 'gt_cost_meta', 'gt_cost_nonce' );

        //foreach of order items, show the cost price box.
        foreach ($order->get_items() as $key => $item)
        {
            $cost = wc_get_order_item_meta( $key, '_gt_cost_price', true );
            ?>
            <p>
    			<label class="meta-label" for="gt_plugin_zone_price">
                    Cost Price
                    (<?php echo $item->get_name(); ?>)
                </label>
                <input type="hidden" name="gt_product_id[]" value="<?php echo $item->get_product_id(); ?>">
                <input type="hidden" name="gt_meta_id[]" value="<?php echo $key; ?>">
    			<input type="text" placeholder="Cost Price"
                    name="gt_cost_price[]" class="widefat"
                    value="<?php echo $cost; ?>">
    		</p>
            <?php
        }
    }

    public function save_cost_meta_box($post_id)
    {
        if (! isset($_POST['gt_cost_nonce'])) {
			return $post_id;
		}

		$nonce = $_POST['gt_cost_nonce'];
		if (! wp_verify_nonce( $nonce, 'gt_cost_meta' )) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if (! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$limit = count($_POST['gt_cost_price']);

        for($i = 0; $i < $limit; $i++)
        {
            $product_id = $_POST['gt_product_id'][$i];
            $term_id = $_POST['gt_meta_id'][$i];
            $cost_price = $this->get_money($_POST['gt_cost_price'][$i]);


            if($cost_price == "" || $cost_price == " ")
            {
                $cost_price = 0;
            }

            if(!is_numeric($cost_price))
            {
                $cost_price = 0;
            }

            //check if the item has cost price meta.
            if(wc_get_order_item_meta($term_id, "_gt_cost_price", true) == "")
            {
                //the meta does not exist. so insert itn
                wc_add_order_item_meta($term_id, "_gt_cost_price", $cost_price);
            }
            else {
                wc_update_order_item_meta($term_id, "_gt_cost_price", $cost_price);
            }
        }

    }

    private function get_money($money)
    {
        $regex = '/[\s\,\.\-]/';
        if(preg_match($regex, $money))
        {
            $filter = preg_filter($regex, '', $money);
        }
        else
        {
            $filter = $money;
        }

        return $filter;
    }
}

?>
