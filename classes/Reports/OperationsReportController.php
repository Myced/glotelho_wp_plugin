<?php
namespace App\Reports;


class OperationsReportController
{
    public static function show_report()
    {
        return require_once BASE_DIRECTORY . '/templates/operations_report.php';
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
