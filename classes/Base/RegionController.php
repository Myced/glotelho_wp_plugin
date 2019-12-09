<?php
namespace App\Base;


class RegionController
{

    public function register()
    {
        add_action('init', [$this, 'register_region_taxonomy']);
    }

    public function register_region_taxonomy()
    {
        $taxonomy = "zone_region";

        $args = [
            'hierarchical' => false,
            'label' => 'Zone Region',
            'parent_item'  => null,
            'parent_item_colon' => null,
        ];

        register_taxonomy( $taxonomy, array('zones', 'post'), $args );

    }
}

?>
