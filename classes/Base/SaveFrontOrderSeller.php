<?php
namespace App\Base;

/**
 * Class to save the seller of an order
 */
class SaveFrontOrderSeller
{
    private $taxonomy = "seller";

    private $role_key = "gt_seller_role";

    private $code_key = "gt_seller_code";

    private $name = "_gt_order_data";

    public function register()
    {
        add_action( 'woocommerce_checkout_update_order_meta', [$this, 'save_seller'], 10, 2 );
    }

    public function save_seller($post_id)
    {
        if ( ! empty( $_POST['gt_seller_code'] ) ) {

            $code = $_POST['gt_seller_code'];

            //now get the seller id from the code
            $seller_id = $this->get_seller_id($code);

            $data = [
                'gt_region' => '-1',
                'gt_seller' => $seller_id,
                'gt_town' => '-1'
            ];

            update_post_meta( $post_id, $this->name, $data );
        }
    }

    private function get_seller_id($code)
    {
        $args = array(
            'taxonomy'   => $this->taxonomy,
            'hide_empty' => false,
            'meta_query' => array(
                 array(
                    'key'       => $this->code_key,
                    'value'     => $code,
                    'compare'   => '='
                 )
            )
        );

        //get the terms
        $terms = get_terms($args);


        if(empty($terms))
            return '-1';

        //the first item is the seller
        $seller = $terms[0];

        return $seller->term_id;
    }
}

 ?>
