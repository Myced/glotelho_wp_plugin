<?php
namespace App\Base;


class QuarterController
{

    public function register()
    {
        add_action('init', [$this, 'register_quarter_taxonomy']);

        add_action('zone_quarter_edit_form_fields',[$this, 'edit_form_fields']);
        add_action('zone_quarter_edit_form', [$this, 'edit_form']);

        add_action('zone_quarter_add_form_fields',[$this, 'edit_form_fields']);
        add_action('zone_quarter_add_form',[$this, 'edit_form']);

        //actions to save and edit custom taxonomy field
        add_action('edited_zone_quarter', [$this, 'save_extra_fileds']);
        add_action('created_zone_quarter',[$this, 'save_extra_fileds']);

        //add hooks to initiate custom columns;
        add_filter( 'manage_edit-zone_quarter_columns', [$this, 'set_custom_columns']);
        add_filter( 'manage_zone_quarter_custom_column', [$this, 'set_custom_columns_data'], 10, 3);
    }

    public function register_quarter_taxonomy()
    {
        $taxonomy = "zone_quarter";

        $args = [
            'hierarchical' => false,
            'label' => 'Zone Quarter',
            'parent_item'  => null,
            'parent_item_colon' => null,
        ];

        register_taxonomy( $taxonomy, array('zones'), $args );

    }

    public function edit_form()
    {

    }

    public function set_custom_columns($columns)
    {
        unset($columns['Count']);

        $columns['region'] = "Region";
        $columns['town'] = "Town";

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

        if($column_name == "town")
        {
            //get the region id and get the region
            $town_id  = get_term_meta($term, 'town_id')[0];

            //now get the region
            $town = get_term_by("id", $town_id, "zone_town");

            return $town->name;
        }
    }

    public function edit_form_fields($term = null, $tt_id = null)
    {

        $term_id = $term->term_id;
        $keyRegion = "region_id";
        $keyTown = "town_id";

        $unique = true;

        $selected_region = $region = get_term_meta($term_id, $keyRegion)[0];
        $selected_town = get_term_meta($term_id, $keyTown)[0];


        $regions = get_terms('zone_region',
            array(
            'hide_empty' => false,
            )
        );

        $towns  = get_terms('zone_town', ['hide_empty' => false] );

        ?>

        <?php
        //if we are editing , then show this form
        if(isset($_GET['tag_ID']))
        {

            ?>
            <tr class="form-field form-required term-name-wrap">
    			<th scope="row"><label for="name">Region:</label></th>
    			<td>
                    <select class="" name="region_id" >
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

            <tr class="form-field form-required term-name-wrap">
    			<th scope="row"><label for="name">Town:</label></th>
    			<td>
                    <select class="" name="town_id" >
                        <?php
                        foreach($towns as $town)
                        {
                            ?>
                            <option value="<?php echo $town->term_id; ?>"
                                <?php if($town->term_id == $selected_town ) echo "selected"; ?>>
                                <?php echo $town->name; ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
    			<p class="description">The town in which the quarter if found.</p></td>
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
                    Select the town the quarter is found in.
                </p>
            </div>

            <div class="form-field term-slug-wrap">
            	<label for="tag-region">Town</label>
                <select class="" name="town_id" >
                    <?php
                    foreach($towns as $town)
                    {
                        ?>
                        <option value="<?php echo $town->term_id; ?>">
                            <?php echo $town->name; ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            	<p>
                    The town in which the quarter if found.
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
            $town_id = $_POST['town_id'];

            $keyRegion = "region_id";
            $keyTown  = "town_id";

            //try to ge tthe option and seee if it exists
            $region = get_term_meta($term_id, $keyRegion)[0];
            $town = get_term_meta($term_id, $keyTown)[0];

            $unique = true;

            if(!$region)
            {
                //insert it
                add_term_meta ($term_id, $keyRegion, $region_id, $unique);
            }
            else {
                //update it
                update_term_meta($term_id, $keyRegion, $region_id);
            }

            if(! $town)
            {
                add_term_meta ($term_id, $keyTown, $town_id, $unique);
            }
            else {
                update_term_meta($term_id, $keyTown, $town_id);
            }
        }
    }
}

?>
