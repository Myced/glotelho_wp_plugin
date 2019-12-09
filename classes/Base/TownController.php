<?php
namespace App\Base;


class TownController
{

    public function register()
    {
        add_action('init', [$this, 'register_town_taxonomy']);

        add_action('zone_town_edit_form_fields',[$this, 'edit_form_fields']);
        add_action('zone_town_edit_form', [$this, 'edit_form']);

        add_action('zone_town_add_form_fields',[$this, 'edit_form_fields']);
        add_action('zone_town_add_form',[$this, 'edit_form']);

        //actions to save and edit custom taxonomy field
        add_action('edited_zone_town', [$this, 'save_extra_fileds']);
        add_action('created_zone_town',[$this, 'save_extra_fileds']);

        //add hooks to initiate custom columns;
        add_filter( 'manage_edit-zone_town_columns', [$this, 'set_custom_columns']);
        add_filter( 'manage_zone_town_custom_column', [$this, 'set_custom_columns_data'], 10, 3);
    }

    public function register_town_taxonomy()
    {
        $taxonomy = "zone_town";

        $args = [
            'hierarchical' => false,
            'label' => 'Zone Town',
            'parent_item'  => null,
            'parent_item_colon' => null,
        ];

        register_taxonomy( $taxonomy, array('zones', 'post'), $args );

    }

    public function edit_form()
    {

    }

    public function set_custom_columns($columns)
    {
        unset($columns['count']);

        $columns['region'] = "Region";

        return $columns;
    }

    public function set_custom_columns_data($out, $column_name, $term)
    {
        if($column_name == "region")
        {
            //get the region id and get the region
            $region_id  = get_term_meta($term, 'region_id')[0];

            //now get the region
            $region = get_term_by("id", $region_id, "zone_region");

            return $region->name;
        }

    }

    public function edit_form_fields($term = null, $tt_id = null)
    {

        if($term instanceof \WP_Term)
        {
            $term_id = $term->term_id;
            $key = "region_id";

            $unique = true;

            $selected_region = $region = get_term_meta($term_id, $key)[0];
        }
        else {
            $selected_region = "-1";
        }

        $regions = get_terms('zone_region',
            array(
                'hide_empty' => false,
            )
        );

        ?>

        <?php
        //if we are editing , then show this form
        if(isset($_GET['tag_ID']))
        {

            ?>
            <tr class="form-field form-required term-name-wrap">
    			<th scope="row"><label for="name">Region:</label></th>
    			<td>
                    <select class="" name="region_id">
                        <?php
                        foreach($regions as $region)
                        {
                            ?>
                            <option value="<?php echo $region->term_id; ?>"
                                <?php if($region->term_id == $selected_region ) echo "selected"; ?>>
                                <?php echo $region->name; ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
    			<p class="description">Select the region in which the town is found</p></td>
    		</tr>
            <?php
        }
        else {
            ?>
            <div class="form-field term-slug-wrap">
            	<label for="tag-region">Region</label>
                <select class="" name="region_id" >
                    <?php
                    foreach($regions as $region)
                    {
                        ?>
                        <option value="<?php echo $region->term_id; ?>">
                            <?php echo $region->name; ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            	<p>
                    Select the region in which the town is found
                </p>
            </div>
            <?php
        }
         ?>


        <?php
    }

    public function save_extra_fileds($term_id)
    {
        if(isset($_POST['region_id']))
        {
            $region_id = $_POST['region_id'];
            $key = "region_id";

            //try to ge tthe option and seee if it exists
            $region = get_term_meta($term_id, $key)[0];
            $unique = true;

            if(!$region)
            {
                //insert it
                add_term_meta ($term_id, $key, $region_id, $unique);
            }
            else {
                //update it
                update_term_meta($term_id, $key, $region_id);
            }
        }
    }
}

?>
