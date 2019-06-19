<?php
namespace App\Base;

use App\Traits\ZoneTrait;

class ZoneController
{
    use ZoneTrait;

    // public $zone_data_key = "_gt_plugin_zone_key"; already in the trait

    public function register()
    {
        add_action('init', [$this, 'register_custom_post_type']);
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_meta_box' ) );
        add_action( 'manage_zones_posts_columns', array( $this, 'set_custom_columns' ) );
        add_action( 'manage_zones_posts_custom_column', array( $this, 'set_custom_columns_data' ), 10, 2 );

        //add an action to remove custom meta boxes
        add_action('admin_menu', [$this, 'remove_meta_boxes']);
    }

    public function register_custom_post_type()
    {
        register_post_type('Zones',
            [
                'public' => true,
                'label' => "Zones",
                "labels" => [
                    "name" => 'Zones',
                    "singular_name" => "Zone",
                    "add_new" => "Add New Zone",
                    "add_new_item" => "Add New Zone"
                ],
                'menu_icon' => "dashicons-buddicons-topics"
            ]
        );
    }

    public function add_meta_boxes()
	{
		add_meta_box(
			'testimonial_author',
			'Zone Options',
			array( $this, 'render_features_box' ),
			'zones',
			'side',
			'default'
		);
	}

    public function render_features_box($post)
	{
		wp_nonce_field( 'gt_plugin_zone', 'gt_plugin_zone_nonce' );

        //we need to get taxonomies for regions, towns and quarters

        //get the list of all the regions, towns and quarters
        $regions = $this->getTerms("zone_region");
        $towns = $this->getTerms("zone_town");
        $quarters = $this->getTerms("zone_quarter");


		$data = $this->getZoneData($post->ID);

		$price = isset($data['price']) ? $data['price'] : '';
        $region = isset($data['region']) ? $data['region'] : '';
		$town = isset($data['town']) ? $data['town'] : '';
        $quarter = isset($data['quarter']) ? $data['quarter'] : '';
		$active = isset($data['active']) ? $data['active'] : false;

		?>
		<p>
			<label class="meta-label" for="gt_plugin_zone_price">Zone Price</label>
			<input type="text" id="gt_plugin_zone_price"
                name="gt_plugin_zone_price" class="widefat"
                placeholder="1000"
                value="<?php echo esc_attr( $price ); ?>">
		</p>
		<p>
			<label class="meta-label" for="gt_plugin_region">Region</label> <br>
            <select class="" name="gt_plugin_zone_region" id="gt_plugin_region">
                <?php
                foreach($regions as $regionO)
                {
                    ?>
                <option value="<?php echo $regionO->term_id; ?>"
                    <?php if($regionO->term_id == $region) { echo 'selected'; } ?> >
                    <?php echo $regionO->name; ?>
                </option>
                    <?php
                }
                 ?>
            </select>

		</p>

        <p>
			<label class="meta-label" for="gt_plugin_zone_town">Town</label>
            <br>
            <select id="gt_plugin_zone_town" name="gt_plugin_zone_town">
                <?php
                foreach($towns as $townO)
                {
                    ?>
                <option value="<?php echo $townO->term_id; ?>"
                    <?php if($townO->term_id == $town) { echo 'selected'; } ?> >
                    <?php echo $townO->name; ?>
                </option>
                    <?php
                }
                 ?>
            </select>
		</p>

        <p>
			<label class="meta-label" for="gt_plugin_zone_quarter">Quarter</label>
            <br>
            <select id="gt_plugin_zone_quarter" name="gt_plugin_zone_quarter">
                <?php
                foreach($quarters as $quarterO)
                {
                    ?>
                <option value="<?php echo $quarterO->term_id; ?>"
                    <?php if($quarterO->term_id == $quarter) { echo 'selected'; } ?> >
                    <?php echo $quarterO->name; ?>
                </option>
                    <?php
                }
                 ?>
            </select>
		</p>

		<div class="meta-container">
			<label class="meta-label w-50 text-left" for="gt_plugin_zone_active_status">Active</label>
			<div class="text-right w-50 inline">
				<div class="ui-toggle inline">
                    <input type="checkbox" id="gt_plugin_zone_active_status"
                        name="gt_plugin_zone_active_status" value="1"
                        <?php echo $active ? 'checked' : ''; ?>>
					<label for="cedplugin_zone_active_status"><div></div></label>
				</div>
			</div>
		</div>
		<?php
	}

    public function save_meta_box($post_id)
	{
		if (! isset($_POST['gt_plugin_zone_nonce'])) {
			return $post_id;
		}

		$nonce = $_POST['gt_plugin_zone_nonce'];
		if (! wp_verify_nonce( $nonce, 'gt_plugin_zone' )) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if (! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$data = array(
			'price' => sanitize_text_field( $_POST['gt_plugin_zone_price'] ),
			'region' => sanitize_text_field( $_POST['gt_plugin_zone_region'] ),
			'town' => sanitize_text_field( $_POST['gt_plugin_zone_town'] ),
            'quarter' => sanitize_text_field( $_POST['gt_plugin_zone_quarter'] ),
			'active' => isset($_POST['gt_plugin_zone_active_status']) ? 1 : 0,
		);

		update_post_meta( $post_id, $this->zone_data_key, $data );
	}

    public function set_custom_columns($columns)
	{
		$title = $columns['title'];
		$date = $columns['date'];
		unset( $columns['title'], $columns['date'] );

		$columns['title'] = $title;
        $columns['price'] = "Price";
		$columns['region'] = 'Region';
		$columns['town'] = 'Town';
		$columns['quarter'] = "Quarter";
        $columns['active'] = "Active";
        $columns['date'] = $date;

		return $columns;
	}

    public function set_custom_columns_data($column, $post_id)
	{
		$data = $this->getZoneData($post_id);

		$price = isset($data['price']) ? $data['price'] : '';
		$region_id = isset($data['region']) ? $data['region'] : '';
        $town_id = isset($data['town']) ? $data['town'] : '';
        $quarter_id = isset($data['quarter']) ? $data['quarter'] : '';
        // var_dump($data); die();
		$active = isset($data['active']) && $data['active'] === 1 ? '<strong>YES</strong>' : 'NO';

		switch($column) {

            case 'price':
				echo $price;
				break;

            case 'region':
                $region = get_term_by("id", $region_id, "zone_region");
                echo $region->name;
				break;

            case 'town':
				$town = get_term_by("id", $town_id, "zone_town");
                echo $town->name;
				break;

            case 'quarter':
				$quarter = get_term_by("id", $quarter_id, "zone_quarter");
                echo $quarter->name;
				break;

			case 'active':
				echo $active;
				break;
		}
	}

    public function remove_meta_boxes()
    {
        remove_meta_box( 'tagsdiv-zone_town', 'zones', 'normal' );
        remove_meta_box( 'tagsdiv-zone_quarter', 'zones', 'normal' );
        remove_meta_box( 'tagsdiv-zone_region', 'zones', 'normal' );
    }
}

?>
