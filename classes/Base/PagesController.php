<?php
namespace App\Base;


class PagesController
{
    public function admin_dashboard()
    {
        return require_once BASE_DIRECTORY . '/templates/admin_dashboard.php';
    }

    public function add_zone()
    {

    }

    public function towns()
    {
        return require_once BASE_DIRECTORY . '/templates/towns.php';
    }

    public function regions()
    {
        return require_once BASE_DIRECTORY . '/templates/regions.php';
    }

}

?>
