<?php
/**
* Plugin Activation class
**/
namespace App\Base;

/**
 *
 */
class Enqueue
{
    //the variable that will contain the glotelho pages
    public $pages = [
        'gt_plugin_settings',
        'gt_regions_settings'
    ];

    public function register()
    {
        //we don't need admin scripts for now.
        add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
    }

    public function enqueue_styles()
    {
        //pages that we will load this script
        $pages = [
            "gt_plugin_settings",
            "gt_orders_report",
            "gt_operations_report",
            "gt_categories_report",
            "gt_monthly_report",
            "gt_income_report"
        ];

        if(isset($_GET['page']))
        {
            $page = $_GET['page'];

            if(in_array($page, $pages))
            {
                wp_enqueue_style(PLUGIN_NAME . 'Bootstrap', GT_ASSETS_URL . 'admin/bootstrap.css' );

                //enqueue moris js
                wp_enqueue_style(PLUGIN_NAME . 'MorrisCss', GT_ASSETS_URL . 'admin/plugins/morris/morris.css' );
                wp_enqueue_style(PLUGIN_NAME . 'DatepickerCss', GT_ASSETS_URL . 'admin/plugins/datepicker/bootstrap-datepicker.css' );
                wp_enqueue_style(PLUGIN_NAME . 'DataTableCss', GT_ASSETS_URL . 'admin/plugins/datatables/dataTables.bootstrap.css' );

                wp_enqueue_style(PLUGIN_NAME . 'AdminLTE', GT_ASSETS_URL . 'admin/AdminLTE.css' );


                //enqueue the final script and css for the page
                wp_enqueue_script(PLUGIN_NAME . 'RaphaelJs', GT_ASSETS_URL . 'admin/plugins/raphael/raphael.min.js' );
                wp_enqueue_script(PLUGIN_NAME . 'MorrisJs', GT_ASSETS_URL . 'admin/plugins/morris/morris.js' );
                wp_enqueue_script(PLUGIN_NAME . 'DatepickerJs', GT_ASSETS_URL . 'admin/plugins/datepicker/bootstrap-datepicker.js' );
                wp_enqueue_script(PLUGIN_NAME . 'DataTableJs', GT_ASSETS_URL . 'admin/plugins/datatables/jquery.dataTables.js' );
                wp_enqueue_script(PLUGIN_NAME . 'DatepickerBsJs', GT_ASSETS_URL . 'admin/plugins/datatables/dataTables.bootstrap.js' );
                wp_enqueue_script(PLUGIN_NAME . 'AdminScript', GT_ASSETS_URL . 'admin/script.js' );
            }
        }



    }
}

 ?>
