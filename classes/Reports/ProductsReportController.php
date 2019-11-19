<?php
namespace App\Reports;

use App\Reports\Managers\ProductsReportManager;

class ProductsReportController
{

    public static function show_report()
    {
        $manager = new ProductsReportManager();

        return require_once BASE_DIRECTORY . '/templates/products_report.php';
    }

}

 ?>
