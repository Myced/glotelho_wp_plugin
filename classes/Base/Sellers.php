<?php
namespace App\Base;


class Sellers
{

    private $role_key = "gt_seller_role";
    private $code_key = "gt_seller_code";

    private $taxonomy = "seller";

    public function register()
    {
        add_action('init', [$this, 'register_region_taxonomy']);

        add_action('seller_add_form_fields',[$this, 'edit_form_fields']);

        add_action('seller_edit_form_fields',[$this, 'edit_form_fields']);

        //add hooks to initiate custom columns;
        add_filter( 'manage_edit-seller_columns', [$this, 'set_custom_columns']);
        add_filter( 'manage_seller_custom_column', [$this, 'set_custom_columns_data'], 10, 3);

        //save the extra data
        add_action('edited_seller', [$this, 'save_extra_fileds']);
        add_action('created_seller',[$this, 'save_extra_fileds']);
    }

    public function register_region_taxonomy()
    {
        $taxonomy = "seller";

        $args = [
            'hierarchical' => false,
            'label' => 'Seller',
            "labels" => [
                "name" => 'Sellers',
                "singular_name" => "Seller",
                "add_new" => "Add New Seller",
                "add_new_item" => "Add New Seller"
            ],
            'parent_item'  => null,
            'parent_item_colon' => null,
        ];

        register_taxonomy( $taxonomy, array('zones'), $args );

    }

    public function save_extra_fileds($term_id)
    {
        if(isset($_POST['gt_seller_role']))
        {
            $role = isset($_POST['gt_seller_role']) ? $_POST['gt_seller_role'] : "";
            $code = isset($_POST['gt_seller_code']) ? $_POST['gt_seller_code'] : "";
            $single = true;



            //try to get the seller role and code
            $seller_role = get_term_meta($term_id, $this->role_key, $single);
            $seller_code = get_term_meta($term_id, $this->code_key, $single);

            if(empty($seller_code))
            {
                $code = $this->generateCode($role);

                //insert it
                add_term_meta ($term_id, $this->code_key, $code, $single);

                //role
                add_term_meta ($term_id, $this->role_key, $role, $single);
            }
            else {
                //update it
                //NO UPDATES ARE ALLOWED ON THE CODE OR USER LEVEL.
                // update_term_meta($term_id, $key, $region_id);
            }
        }
    }

    public function generateCode($role)
    {
        //get the sellers in that type
        $sellers = $this->getSellersByRole($role);

        $count = count($sellers);
        ++$count;

        $code = $this->normalise($count);

        $user_code = $role . $code;

        return $user_code;
    }

    private function normalise($count)
    {
        if($count < 10)
        {
            return "00" . $count;
        }
        elseif ($count < 100) {
            return "0" . $count;
        }
        else {
            return $count;
        }
    }

    public function getSellersByRole($role)
    {
        $args = array(
            'taxonomy'   => $this->taxonomy,
            'hide_empty' => false,
            'meta_query' => array(
                 array(
                    'key'       => $this->role_key,
                    'value'     => $role,
                    'compare'   => '='
                 )
            )
        );

        //get the terms
        $terms = get_terms($args);

        return $terms;
    }

    public function edit_form_fields($term = null, $tt_id = null)
    {
        $code = "";
        $role = "";

        if($term instanceof \WP_Term)
        {
            $term_id = $term->term_id;
            $key = $this->code_key;

            $single = true;

            $code = get_term_meta($term_id, $key, $single);
            $role = get_term_meta($term_id, $this->role_key, $single);

        }
        else {

        }

        //get the seller code

        ?>

        <?php
        //if we are editing , then show this form
        if(isset($_GET['tag_ID']))
        {
            ?>
            <tr class="form-field form-required term-name-wrap">
    			<th scope="row"><label for="name">Seller Role:</label></th>
    			<td>
                    <?php
                    if($role != "")
                    {
                        ?>
                        <input type="hidden" name="gt_seller_role" value="<?php echo $role; ?>">
                        <?php
                    }
                     ?>
                    <select class="form-control" name="gt_seller_role"
                        <?php if($role != "") echo "disabled"; ?>>
                        <option value="SC" <?php if($role == "SC") echo 'selected'; ?> >Service Client</option>
                        <option value="AM" <?php if($role == "AM") echo 'selected'; ?> >Ambassador</option>
                        <option value="FL" <?php if($role == "FL") echo 'selected'; ?> >FreeLance</option>
                        <option value="EM" <?php if($role == "EM") echo 'selected'; ?> >Employee</option>
                    </select>
                    <p class="description">Select the role of the seller</p>
                </td>
    		</tr>

            <tr class="form-field form-required term-name-wrap">
    			<th scope="row"><label for="name">Code:</label></th>
    			<td>
                    <p>
                        <strong> <?php echo $code ?> </strong>
                    </p>
                </td>
    		</tr>
            <?php
        }
        else {

            ?>

            <div class="form-field term-slug-wrap">
                <label for="tag-region">Seller Role</label>
                <select class="form-control" name="gt_seller_role">
                    <option value="SC">Service Client</option>
                    <option value="AM">Ambassador</option>
                    <option value="FL">FreeLance</option>
                    <option value="EM">Employee</option>
                </select>
                <p class="description">Select the role of the seller</p>
            </div>

            <div class="form-field term-slug-wrap">
                <label for="tag-region">Seller Code</label>
                <input type="text" name="gt_seller_code" value="" placeholder="Code (Filled Automatically) " readonly>
                <p class="description">Enter the Seller code or leave it blank for autogeneration</p>
            </div>
            <?php
        }
         ?>


        <?php
    }

    public function edit_form()
    {


    }

    public function set_custom_columns($columns)
    {
        unset($columns['posts']);
        unset($columns['slug']);


        $columns['role'] = "Role";
        $columns['code'] = "Code";

        return $columns;
    }

    public function set_custom_columns_data($out, $column_name, $term)
    {
        if($column_name == "role")
        {
            //get the region id and get the region
            $role = get_term_meta($term, $this->role_key, true);
            $role_name = "";

            if($role == "SC")
                $role_name = "Service Client";

            if($role == "FL")
                $role_name = "Freelance";

            if($role == "AM"){
                $role_name = "Ambassadeur";
            }

            echo  $role_name;
        }

        if($column_name == "code")
        {
            $code = get_term_meta($term, $this->code_key, true);

            echo "<strong> $code </strong>";
        }



    }

}

?>
