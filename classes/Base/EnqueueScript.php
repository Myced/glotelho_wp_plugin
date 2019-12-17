<?php
/**
* Plugin Activation class
**/
namespace App\Base;

/**
 *
 */
class EnqueueScript
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
            "gt_income_report",
            "gt_sellers_report",
            "gt_sales_report",
            "gt_eod_report",
            "gt_products_report",
            "gt_top_sellers",
            "gt_client_achat",
            "gt_order_statuses",
            "gt_freelance_report"
        ];

        if(isset($_GET['page']))
        {
            $page = $_GET['page'];

            if(in_array($page, $pages))
            {
                wp_enqueue_style(GT_PLUGIN_NAME . 'Bootstrap', GT_ASSETS_URL . 'admin/bootstrap.css' );

                //enqueue moris js
                wp_enqueue_style(GT_PLUGIN_NAME . 'MorrisCss', GT_ASSETS_URL . 'admin/plugins/morris/morris.css' );
                wp_enqueue_style(GT_PLUGIN_NAME . 'DatepickerCss', GT_ASSETS_URL . 'admin/plugins/datepicker/bootstrap-datepicker.css' );
                wp_enqueue_style(GT_PLUGIN_NAME . 'DataTableCss', GT_ASSETS_URL . 'admin/plugins/datatables/dataTables.bootstrap.css' );
                wp_enqueue_style(GT_PLUGIN_NAME . 'Glyphicon', GT_ASSETS_URL . 'glyphicon.css' );
                wp_enqueue_style(GT_PLUGIN_NAME . 'AdminLTE', GT_ASSETS_URL . 'admin/AdminLTE.css' );
                wp_enqueue_style(GT_PLUGIN_NAME . 'CustomStyle', GT_ASSETS_URL . 'mystyle.css' );

                //enqueue chosen js
                wp_enqueue_style(GT_PLUGIN_NAME . 'ChosenCss', GT_ASSETS_URL . 'admin/plugins/chosen/chosen.css' );
                wp_enqueue_script(GT_PLUGIN_NAME . 'ChosenJs', GT_ASSETS_URL . 'admin/plugins/chosen/chosen.jquery.js' );
                wp_enqueue_script(GT_PLUGIN_NAME . 'Bootstrap', GT_ASSETS_URL . 'admin/bootstrap.js' );

                //enqueue the final script and css for the page
                wp_enqueue_script(GT_PLUGIN_NAME . 'RaphaelJs', GT_ASSETS_URL . 'admin/plugins/raphael/raphael.min.js' );
                wp_enqueue_script(GT_PLUGIN_NAME . 'MorrisJs', GT_ASSETS_URL . 'admin/plugins/morris/morris.js' );
                wp_enqueue_script(GT_PLUGIN_NAME . 'DatepickerJs', GT_ASSETS_URL . 'admin/plugins/datepicker/bootstrap-datepicker.js' );
                wp_enqueue_script(GT_PLUGIN_NAME . 'DataTableJs', GT_ASSETS_URL . 'admin/plugins/datatables/jquery.dataTables.js' );
                wp_enqueue_script(GT_PLUGIN_NAME . 'DatepickerBsJs', GT_ASSETS_URL . 'admin/plugins/datatables/dataTables.bootstrap.js' );
                wp_enqueue_script(GT_PLUGIN_NAME . 'AdminScript', GT_ASSETS_URL . 'admin/script.js' );
            }
        }



    }
}

 ?>
