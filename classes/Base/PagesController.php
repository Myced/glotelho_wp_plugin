<?php
namespace App\Base;


class PagesController
{
    public static function admin_dashboard()
    {
        return require_once GT_BASE_DIRECTORY . '/templates/admin_dashboard.php';
    }

    public function add_zone()
    {

    }

    public function towns()
    {
        return require_once GT_BASE_DIRECTORY . '/templates/towns.php';
    }

    public function regions()
    {
        return require_once GT_BASE_DIRECTORY . '/templates/regions.php';
    }

}

?>
