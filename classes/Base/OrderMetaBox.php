<?php
namespace App\Base;

use App\Base\Users;
use App\Traits\ZoneTrait;

class OrderMetaBox
{
    use ZoneTrait;

    private $name = "_gt_order_data";
    const NAME = "_gt_order_data";

    public function register()
    {
        add_action( 'add_meta_boxes', [$this, 'add_meta_box'] );

        add_action( 'save_post', [$this, 'save_meta_data'], 10, 1 );

        add_action('admin_footer', [$this, 'init_script']);
    }

    public function add_meta_box()
    {
        add_meta_box( 'mv_other_fields', __('Order Data','woocommerce'),
            [$this,'add_order_box_fields'], 'shop_order', 'side', 'core' );
    }

    private function get_authorized_users()
    {
        return Users::authorized();
    }

    private function current_username()
    {
        $user = wp_get_current_user();

        return $user->user_login;
    }

    public function add_order_box_fields()
    {
        global $post;

        $old_status = $post->post_status;
        $authorized_users = json_encode($this->get_authorized_users());
        $current_user = $this->current_username();

        $meta_data = get_post_meta( $post->ID, $this->name, true );

        if($meta_data == '')
        {
            $gt_region = '-1';
            $gt_seller = '-1';
            $gt_town = '-1';
        }
        else {
            $gt_region = isset($meta_data['gt_region']) ? $meta_data['gt_region'] : '';
            $gt_seller = isset($meta_data['gt_seller']) ? $meta_data['gt_seller'] : '';
            $gt_town = isset($meta_data['gt_town']) ? $meta_data['gt_town'] : '';
        }

        echo '<input type="hidden" name="gt_order_nonce" value="' . wp_create_nonce() . '">';
        echo '<input type="hidden" id="gt_b_old_status"
                name="gt_b_old_status" value="' . $old_status . '">';

        echo "<input type=\"hidden\" id=\"gt_auth_users\"
                name=\"gt_auth_users\" value='$authorized_users'>";

        echo '<input type="hidden" id="gt_b_current_user"
                name="gt_b_current_user" value="' . $current_user . '">';

        ?>
        <p>
			<label class="meta-label" for="gt_plugin_seller">Order Seller:</label>

            <select class="form-controll gt_seller" name="gt_seller" id="gt_plugin_seller"
                style="width: 200px; ">
                <option value="-1">Select Seller</option>

                <?php foreach ($this->getSellers() as $seller): ?>
                    <option value="<?php echo $seller->term_id; ?>"
                        <?php
                        if($gt_seller != '-1')
                        {
                            if($gt_seller == $seller->term_id)
                                echo "selected";
                        }

                        $code = get_term_meta($seller->term_id, "gt_seller_code", true);

                         ?>
                        >
                        <?php echo $seller->name . ' (' . $code . ')'; ?>
                    </option>
                <?php endforeach; ?>

            </select>
		</p>

        <p>
			<label class="meta-label" for="gt_plugin_region">Region:</label>

            <select class="form-controll" name="gt_region" id="gt_plugin_region"
                style="width: 100px; ">
                <option value="-1">Select Region</option>
                <?php foreach ($this->getRegions() as $region): ?>
                    <option value="<?php echo $region->term_id; ?>"
                        <?php
                        if($gt_region != '-1')
                        {
                            if($gt_region == $region->term_id)
                                echo "selected";
                        }
                         ?>
                        >
                        <?php echo $region->name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
		</p>

        <p>
			<label class="meta-label" for="gt_plugin_town">Town / Ville:</label>

            <select class="form-controll" name="gt_town" id="gt_plugin_town"
                style="width: 100px; ">
                <option value="-1">Select Town</option>

                <?php foreach ($this->getTowns() as $town): ?>

                    <option value="<?php echo $town->term_id; ?>"
                        <?php
                            if($gt_town == $town->term_id)
                                echo "selected";
                         ?>
                        >
                        <?php echo $town->name; ?>
                    </option>
                <?php endforeach; ?>

            </select>
		</p>

        <?php

    }

    public function save_meta_data( $post_id ) {

        // We need to verify this with the proper authorization (security stuff).

        // Check if our nonce is set.
        if ( ! isset( $_POST[ 'gt_order_nonce' ] ) ) {
            return $post_id;
        }
        $nonce = $_REQUEST[ 'gt_order_nonce' ];

        //Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce ) ) {
            return $post_id;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        // Check the user's permissions.
        if ( 'page' == $_POST[ 'post_type' ] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {

            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
        // --- Its safe for us to save the data ! --- //

        $data = [
            'gt_region' => $_POST['gt_region'],
            'gt_seller' => $_POST['gt_seller'],
            'gt_town' => $_POST['gt_town']
        ];

        // var_dump($post_id); die();
        // Sanitize user input  and update the meta field in the database.
        update_post_meta( $post_id, $this->name, $data );

        //check if the sender has been set.
    }

    public function getRegions()
    {
        return $this->getTerms("zone_region");
    }

    public function getSellers()
    {
        return $this->getTerms('seller');
    }

    private function init_seller_codes()
    {

    }

    public function getTowns()
    {
        return $this->getTerms('zone_town');
    }

    public function init_script()
    {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                if(jQuery('.gt_seller').val() !== undefined )
                {
                    $('.gt_seller').select2();
                }

                jQuery('#post').on('submit', function(event){

                    //get the required variables
                    var old_status = $("#gt_b_old_status").val();
                    var new_status = $("#order_status").val();
                    var authorized_users = $.parseJSON($("#gt_auth_users").val());
                    var current_user = $("#gt_b_current_user").val();

                    var paid_status = "wc-payment-received";

                    if(old_status === paid_status)
                    {
                        //no more modifications for unauthorized users
                        if(authorized_users.includes(current_user))
                        {
                            return true;
                        }
                        else {
                            alert("Vous ne pouvez plus modifier cette commande car elle a déjà été Encaissé");
                            alert("Veuillez contacter la comptabilité");
                        }
                    }
                    else {
                        if(new_status !== paid_status)
                        {
                            //you can submit the form
                            return true;
                        }
                        else {
                            //if the user is authroized, the submit
                            if(authorized_users.includes(current_user))
                            {
                                return true;
                            }
                            else {

                                alert("vous n'avez pas l'autorisation de Changer ce status à Encaissé, veuillez contacter votre responsable ");

                            }

                        }
                    }

                    return false;

                })
            });
        </script>
        <?php
    }
}

 ?>
