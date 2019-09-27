<?php
namespace App\Base;

use App\Base\GTShipping;
use App\Traits\ZoneTrait;

class Checkout
{
    use ZoneTrait;

    private $zone_cookie_set = false;
    private $zone_id;
    private $zone;

    private $region_id;
    private $town_id;
    private $quarter_id;

    private $price;

    public function register()
    {
        add_action('wp_head', [$this, 'loadCheckoutCss']);
        add_action("woocommerce_checkout_shipping", [$this, 'afterUserDetails']);

        //remove all default shipping form fields.
        //we don't need them
        //taken care of by woocommerce checkout form plugin.
        // add_filter("woocommerce_shipping_fields", [$this, 'removeDefaultFields']);
    }

    public function removeDefaultFields($fields)
    {
        foreach($fields as $key => $value)
        {
            unset($fields[$key]);
        }

        return $fields;
    }

    public function afterUserDetails()
    {
        return $this->showZoneButton();
    }

    public function loadCheckoutCss()
    {
        ?>
        <style media="screen">
            .btn-flat{
                border-radius: 0px;
            }

            .br-gray
            {

            }

            .m-t-10
            {
                margin-top: 10px;
            }
        </style>
        <?php
    }

    public function showZoneButton()
    {
        //check if the zone cookie has been set
        $this->validateCookie();
        ?>
        <!-- <div class="row">
            <div class="col-md-12">
                <h4 class="page-header">Détails de livraison</h4>
            </div>
        </div>

        <br>
        <div class="row show" id="choose_zone">
            <div class="col-md-12">
                <button type="button" class="popup btn btn-info btn-flat"
                    name="button">
                    Choisir Votre Zone
                </button>
            </div>
        </div> -->


        <br><br>
        <div id="le-zone" class="white-popup mfp-hide" style="z-index: 8888;">
            <div class="" id="gt-loading-image">
                &nbsp;
            </div>

                <div class="row">
                    <div class="col-md-12">
                        <h2 class="gt-page-header m-t-10" >Choisir Votre Zone</h2>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12">

                        <div class="form-group">
                            <label for="" class="control-label ">
                                Région/Region:
                            </label>

                            <select class="form-controll" name="region" id="region">
                                <option value="-01">--Sélectionner la région--</option>
                                <?php
                                foreach($this->getRegions() as $region)
                                {
                                    ?>
                                    <option value="<?php echo $region->term_id; ?>"
                                        <?php if($this->zone_cookie_set)
                                                    {
                                                        if($region->term_id == $this->region_id)
                                                        { echo 'selected'; }
                                                    } ?>
                                        >
                                        <?php echo $region->name; ?>
                                    </option>
                                    <?php
                                }
                                ?>

                            </select>
                        </div>

                    </div>

                    <div class="col-md-6 col-sm-12 col-xs-12">

                        <div class="form-group">
                            <label for="" class="control-label ">
                                Town/Ville:
                            </label>

                            <?php
                            if($this->zone_cookie_set)
                            {
                                $towns = $this->getTowns();
                                ?>
                                <select class="form-controll" name="town" id="town">
                                    <?php foreach ($towns as $town): ?>
                                        <option value="<?php echo $town->term_id ?>"
                                            <?php if ($town->term_id == $this->town_id): ?>
                                                <?php echo 'selected'; ?>
                                            <?php endif; ?>>
                                            <?php echo $town->name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php
                            }
                            else {
                                ?>
                            <select class="form-controll" name="town" id="town">
                                <option value="-01">Sélectionner la région</option>
                            </select>
                                <?php
                            }
                             ?>
                        </div>

                    </div>
                </div>

                <!-- next row -->
                <br>
                <div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12">

                        <div class="form-group">
                            <label for="" class="control-label ">
                                Quarter/Quartier:
                            </label>

                            <?php
                            if ($this->zone_cookie_set)
                            {
                                $quarters = $this->getQuarters();

                                ?>
                                <select class="form-controll" name="quarter" id="quarter">
                                    <?php
                                    foreach ($quarters as $quarter)
                                    {
                                        ?>
                                        <option value="<?php echo $quarter->term_id ?>"
                                            <?php if($this->quarter_id == $quarter->term_id) { echo 'selected'; } ?>>
                                            <?php echo $quarter->name; ?>
                                        </option>
                                        <?php
                                    }
                                     ?>
                                </select>
                                <?php
                            }
                            else {
                                ?>
                            <select class="form-controll" name="quarter" id="quarter">
                                <option value="-01">Sélectionner la ville</option>
                            </select>
                                <?php
                            }
                             ?>
                        </div>

                    </div>

                    <div class="col-md-6 col-sm-12 col-xs-12">

                        <div class="form-group">
                            <label for="" class="control-label ">
                                Zone:
                            </label>

                            <?php
                            if($this->zone_cookie_set)
                            {
                                $customZones = $this->getCustomZones();

                                ?>
                                <select class="form-controll" name="gt_zone" id="zone">
                                    <?php
                                    foreach($customZones as $zone)
                                    {
                                        ?>
                                        <option value="<?php echo $zone->ID; ?>"
                                            <?php if($zone->ID == $this->zone_id) { echo "selected"; } ?>>
                                            <?php echo $zone->post_title; ?>
                                        </option>
                                        <?php
                                    }
                                     ?>
                                </select>
                                <?php

                            }
                            else {
                                ?>
                                <select class="form-controll" name="gt_zone" id="zone">
                                    <option value="-01">Sélectionner le quartier</option>
                                </select>
                                <?php
                            }
                             ?>
                        </div>

                    </div>
                </div>
                <!-- end of next row -->

                <!-- details row  -->
		<br>
                <div class="row <?php if(empty($this->zone_id)) { echo "hide"; } ?>" id="zone_hide">
                    <div class="col-md-12">
                        <div class="details">
                            <p>
                                Détails de zone
                            </p>

                            <h4 class="checkout">
                                <strong>Zone: </strong>

                                <?php if ($this->zone_cookie_set): ?>
                                    <span id="zone_title">
                                        <?php echo $this->zone->post_title; ?>
                                    </span>
                                <?php else: ?>
                                    <span id="zone_title">
                                        --
                                    </span>
                                <?php endif; ?>


                            </h4>

                            <h4 class="checkout">
                                <strong>Coût: </strong>

                                <?php if ($this->zone_cookie_set): ?>
                                    <span id="cost"><?php echo $this->price; ?></span>
                                <?php else: ?>
                                    <span id="cost">--</span>
                                <?php endif; ?>
                                FCFA
                            </h4>

                            <br>
                            <h4 class="checkout"> <strong>Détails</strong> </h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="description">
                                        <?php if ($this->zone_cookie_set): ?>
                                            <?php echo nl2br($this->zone->post_content); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
                <!-- end of details row -->

                <!-- //confirmation row  -->
                <br><br>
                <div class="row">
                    <div class="col-md-12">
                        <div class="float-right">
                            <button type="button" class="btn btn-flat btn-primary" name="button"
                                id="close-btn">
                                Choisir Cette Zone
                            </button>
                        </div>
                    </div>
                </div>
                <!-- end of confirmation row -->
            </div>
        <?php
    }

    private function validateCookie()
    {
        //check if the cookie has been set
        if(isset($_COOKIE[GTShipping::COOKIE_NAME]))
        {
            //grab the value
            $zone_id = $_COOKIE[GTShipping::COOKIE_NAME];

            //try to get the zone and see if its null
            $zone = $this->getZone($zone_id);

            if(! is_null($zone))
            {
                $this->zone_cookie_set = true;
                $this->zone_id = $zone_id;
                $this->zone = $zone;

                //get the zone data and set it
                $data = $this->getZoneData($zone_id);

                $this->region_id = isset($data['region']) ? $data['region'] : '';
                $this->town_id = isset($data['town']) ? $data['town'] : '';
                $this->quarter_id = isset($data['quarter']) ? $data['quarter'] : '';
                $this->price = isset($data['price']) ? $data['price'] : '';
            }
        }
    }

    private function getRegions()
    {
        return get_terms('zone_region',['hide_empty' => false ]);
    }

    private function getTowns()
    {
        //get the towns based on the region selected
        $term_name = 'zone_town';
        $meta_key = "region_id";
        $meta_value = $this->region_id;

        return $this->getTermByMeta($term_name, $meta_key, $meta_value);
    }

    private function getQuarters()
    {
        $term_name = 'zone_quarter';
        $meta_key = "town_id";
        $meta_value = $this->town_id;

        return $this->getTermByMeta($term_name, $meta_key, $meta_value);
    }

    private function getCustomZones()
    {
        $quarter_id = $this->quarter_id;

        $zones = $this->getZones();

        return $this->getSelectedZones($quarter_id, $zones);
    }

    private function getSelectedZones($quarter_id, $zones)
    {
        $selectedZones = [];

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

        return $selectedZones;
    }

}

?>
