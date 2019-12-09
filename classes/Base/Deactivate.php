<?php
/**
* Plugin Activation class
**/
namespace App\Base;

/**
 *
 */
class Deativate
{

    public static function deactivate()
    {
        flush_rewrite_rules();
    }
}

 ?>
