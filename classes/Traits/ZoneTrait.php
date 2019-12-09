<?php
namespace App\Traits;

trait ZoneTrait
{
    public $zone_data_key = "_gt_plugin_zone_key";

    public function getZones()
    {
        $args = [
            "post_type" => "Zones",
            "nopaging" => true,

        ];

        $zones = get_posts($args);
        return $zones;
    }

    public function getZone($zone_id)
    {
        return get_post($zone_id);
    }

    public function getZoneData($zone_id)
    {
        return get_post_meta( $zone_id, $this->zone_data_key, true );
    }

    public function getTerms($type)
    {
        return get_terms($type, ['hide_empty' => false ]);
    }

    public function getTermByMeta($term_name, $meta_key, $meta_value)
    {
        $result = get_terms($term_name,
            [
                'hide_empty' => false,
                'meta_query' => [
                    [
                        'key'       => $meta_key,
                        'value'     => $meta_value,
                        'compare'   => '='
                    ]
                ]
            ]
        );

        return $result;
    }
}

 ?>
