<?php
namespace App\Reports;

use Managers\ProductsReportManager;

class ProductsReportController
{

    public function show_report()
    {
        $manager = new ProductsReportManager();
    }
}

 ?>
