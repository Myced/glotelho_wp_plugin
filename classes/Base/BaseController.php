<?php
namespace App\Base;

/**
 *
 */
class BaseController
{
    protected $plugin_path;

    public function __construct()
    {
        $this->plugin_path = GT_PLUGIN_URL;
    }
}


?>
