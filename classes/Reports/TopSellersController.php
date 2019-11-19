<?php
namespace App\Reports;

use App\Reports\Managers\TopSellersManager;

class TopSellersController
{

    public static function show_report()
    {

        $manager = new TopSellersManager;

        return require_once BASE_DIRECTORY . '/templates/top_sellers.php';

    }
}

 ?>
