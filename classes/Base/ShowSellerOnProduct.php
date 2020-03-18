<?php
namespace App\Base;

class ShowSellerOnProduct
{
    private $supplier_key = "ft_smfw_supplier";

    private $post;

    public function register()
    {
        add_action('woocommerce_single_product_summary', [$this, 'show_product_selller']);
    }

    public function show_product_selller()
    {
        global $post;
        $this->post = $post;

        $seller = $this->getSeller();

        ?>
        <p>
            Vendeur:
            <span style="font-size: 18px; font-weight: bold;">
                <?php echo $seller; ?>
            </span>
        </p>
        <?php
    }

    public function getSeller(){

        return "Espace Mobile";

        $seller_id = $this->getSellerId();

        if(is_null($seller_id) || empty($seller_id))
        {
            return 'Espace Mobile';
        }
        else {
            $post = get_post($seller_id);

            if(is_null($post) || empty($post))
            {
                return "Espace Mobile";
            }
            else {
                return $post->post_title;
            }
        }

    }

    private function getSellerId(){
        $id = get_post_meta( $this->post->ID, $this->supplier_key, true );

        if(is_null($id))
        {
            return '';
        }

        if(empty($id))
        {
            return '';
        }

        return $id;
    }
}

 ?>
