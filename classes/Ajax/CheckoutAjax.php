<?php
namespace App\Ajax;

use App\Traits\ZoneTrait;

class CheckoutAjax
{
    use ZoneTrait;

    public function register()
    {
        //get the towns from a particular region
        add_action( 'wp_ajax_cedplugin_get_towns',[$this, 'getTowns'] );
        add_action( 'wp_ajax_nopriv_cedplugin_get_towns', [$this, 'getTowns']);

        //add actions to get the quarters.
        add_action( 'wp_ajax_cedplugin_get_quarters',[$this, 'getQuarters'] );
        add_action( 'wp_ajax_nopriv_cedplugin_get_quarters', [$this, 'getQuarters']);

        //get the zones from here
        add_action( 'wp_ajax_cedplugin_get_zones',[$this, 'getZones'] );
        add_action( 'wp_ajax_nopriv_cedplugin_get_zones', [$this, 'getZones']);

    }

    public function getTowns()
    {
        $region_id = $_POST['region_id'];

        //get the regions now
        $towns = get_terms('zone_town',
            [
                'hide_empty' => false,
                'meta_query' => [
                    [
                        'key'       => 'region_id',
                        'value'     => $region_id,
                        'compare'   => '='
                    ]
                ]
            ]
        );

        echo json_encode($towns);
        wp_die();
    }

    public function getQuarters()
    {
        $town_id = $_POST['town_id'];

        //get the regions now
        $quarters = get_terms('zone_quarter',
            [
                'hide_empty' => false,
                'meta_query' => [
                    [
                        'key'       => 'town_id',
                        'value'     => $town_id,
                        'compare'   => '='
                    ]
                ]
            ]
        );

        echo json_encode($quarters);
        wp_die();
    }

    public function getZones()
    {
        $quarter_id = $_POST['quarter_id'];

        $args = [
            "post_type" => "Zones",
            "nopaging" => true,

        ];

        $zones = get_posts($args);

        $selectedZones = [];

        //now loop through the zones and select those that
        //have the given quarter
        foreach($zones as $zone)
        {
            $data = $this->getZoneData($zone->ID);

            $active = isset($data['active']) ? $data['active'] : false;

            if($active == false)
            {
                continue;
            }

            if(isset($data['quarter']))
            {
                if($data['quarter'] == $quarter_id)
                {
                    //add it to the selected zone
                    //get the price of the zone
                    $price = $data['price'];

                    //override the zone post author with price
                    $zone->post_author = $price;

                    array_push($selectedZones, $zone);
                }
            }
        }

        echo json_encode($selectedZones);
        die();
    }

}

?>
