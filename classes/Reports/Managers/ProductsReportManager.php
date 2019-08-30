<?php
namespace App\Reports\Managers;

class ProductsReportManager
{
    public function __construct()
    {

    }

    public function get_all_products()
    {
        $args = [
            'downloadable' => false
        ];

        return wc_get_products($args);
    }
}

 ?>
