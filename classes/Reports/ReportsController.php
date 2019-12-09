<?php
namespace App\Reports;

use App\Reports\Managers\ReportsManager;

class ReportsController
{
    public static function admin_dashboard()
    {
        $manager = new ReportsManager();

        $stats = $manager->getOrderStats();
        $header_data = $stats['with_shipping'];
        $header_san_shipping = $stats['without_shipping'];

        //prepare data for graph.
        $pieChart = [
            'completed' => $header_data['completed']['count'],
            'pending' => $header_data['pending']['count'],
            'cancelled' => $header_data['cancelled']['count']
        ];

        $barChart = [
            'completed' => $header_san_shipping['completed']['total'],
            'pending' => $header_san_shipping['pending']['total'],
            'cancelled' => $header_san_shipping['cancelled']['total']
        ];

        //get the data for users and region stats
        $regStats = $manager->userRegionReport();

        $regionData = $regStats['regions'];
        $sellerData = $regStats['sellers'];
        $townData = $regStats['towns'];
        $paymentData = $regStats['payment_methods'];


        return require_once GT_BASE_DIRECTORY . '/templates/admin_dashboard.php';
    }

    public static function getRegions()
    {
        return get_terms('zone_region', ['hide_empty' => false ]);
    }

    public static function getSellers()
    {
        return get_terms('seller', ['hide_empty' => false ]);
    }

    public static function getTowns()
    {
        return get_terms('zone_town', ['hide_empty' => false ]);
    }

    public static function getPaymentMethods()
    {
        return WC()->payment_gateways->get_available_payment_gateways();
    }
}
 ?>
