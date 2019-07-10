<?php
namespace App\Base;


class Sellers
{

    public function register()
    {
        add_action('init', [$this, 'register_region_taxonomy']);
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

        register_taxonomy( $taxonomy, array('zones', 'post'), $args );

    }
}

?>
