<?php
/**
* Plugin Activation class
**/
namespace App\Admin;

use App\Api\SettingsApi;

/**
 *
 */
class Admin
{

    public $settings;

    public function register()
    {
        //regiser the admin services.
        //these include custom post type and
        $name = GT_PLUGIN_BASENAME;

        $this->settings = new SettingsApi;

        $pages = $this->getPages();
        $subpages = $this->getSubPages();

        //initialise the custom settings
        $this->settings->setSettings($this->getSettings());
        $this->settings->setSections($this->getSections());
        $this->settings->setFields($this->getFields());

        $this->settings->addSubPages($subpages)->addPages($pages)->register();

    }

    private function getPages()
    {
        $pages = [
            [
                "title" => 'Glotelho Report',
                'menu_title' => "Glotelho Report",
                'capability' => "manage_categories",
                'slug' => "gt_plugin_settings",
                "callback" => ['\App\Reports\ReportsController', 'admin_dashboard'],
                "icon" => "",
                "position" => null
            ]
        ];

        return $pages;
    }

    private function getSubPages()
    {
        $parent_slug = "gt_plugin_settings";
        return $pages = [
            [
                "parent_slug" => $parent_slug,
                "title" => 'Rapport Statut Commandes',
                'menu_title' => "Rapport Statut des Commandes",
                'capability' => "manage_categories",
                'slug' => "gt_order_statuses",
                "callback" => ['\App\Reports\RapportStatutController', 'show_report']
            ],
            [
                "parent_slug" => $parent_slug,
                "title" => 'Comptabilité',
                'menu_title' => "Comptabilité",
                'capability' => "manage_categories",
                'slug' => "gt_accounting",
                "callback" => ['\App\Reports\AccountingReportController', 'show_report']
            ],
            [
                "parent_slug" => $parent_slug,
                "title" => 'Comptabilité Report',
                'menu_title' => "Comptabilité Rapport",
                'capability' => "manage_categories",
                'slug' => "gt_accounting_report",
                "callback" => ['\App\Reports\GTAccountingReportController', 'show_report']
            ],
            [
                "parent_slug" => $parent_slug,
                "title" => 'Commandes Vendeurs',
                'menu_title' => "Commandes Vendeurs",
                'capability' => "manage_categories",
                'slug' => "gt_seller_orders",
                "callback" => ['\App\Reports\SellerOrderController', 'show_report']
            ],
            [
                "parent_slug" => $parent_slug,
                "title" => 'Rapport Client Achat',
                'menu_title' => "Rapport Client Achat",
                'capability' => "manage_categories",
                'slug' => "gt_client_achat",
                "callback" => ['\App\Reports\ClientAchatController', 'show_report']
            ],
            [
                "parent_slug" => $parent_slug,
                "title" => 'Rapport Ventes',
                'menu_title' => "Rapport Ventes",
                'capability' => "manage_categories",
                'slug' => "gt_ventes_report",
                "callback" => ['\App\Reports\VentesController', 'show_report']
            ],
            [
                "parent_slug" => $parent_slug,
                "title" => 'Sales Report',
                'menu_title' => "Sales Report",
                'capability' => "manage_categories",
                'slug' => "gt_sales_report",
                "callback" => ['\App\Reports\SaleReportController', 'show_report']
            ],
            [
                "parent_slug" => $parent_slug,
                "title" => 'End OF Day Report',
                'menu_title' => "End OF Day Report",
                'capability' => "manage_categories",
                'slug' => "gt_eod_report",
                "callback" => ['\App\Reports\EndOfDayReportController', 'show_report']
            ],
            [
                "parent_slug" => $parent_slug,
                "title" => 'Orders Report',
                'menu_title' => "Orders Report",
                'capability' => "manage_categories",
                'slug' => "gt_orders_report",
                "callback" => ['\App\Reports\OrderReportController', 'show_orders']
            ],
            [
                "parent_slug" => $parent_slug,
                "title" => 'Sellers Report',
                'menu_title' => "Sellers Report",
                'capability' => "manage_categories",
                'slug' => "gt_sellers_report",
                "callback" => ['\App\Reports\SellerReportController', 'show_report']
            ],
            [
                "parent_slug" => $parent_slug,
                "title" => 'Operations Report',
                'menu_title' => "Operations Report",
                'capability' => "manage_categories",
                'slug' => "gt_operations_report",
                "callback" => ['\App\Reports\OperationsReportController', 'show_report']
            ],
            [
                "parent_slug" => $parent_slug,
                "title" => 'Categories Report',
                'menu_title' => "Categories Report",
                'capability' => "manage_categories",
                'slug' => "gt_categories_report",
                "callback" => ['\App\Reports\CategoriesReportController', 'show_report']
            ],
            [
                "parent_slug" => $parent_slug,
                "title" => 'Income Report',
                'menu_title' => "Income Report",
                'capability' => "manage_categories",
                'slug' => "gt_income_report",
                "callback" => ['\App\Reports\IncomeReportController', 'show_report']
            ],
            [
                "parent_slug" => $parent_slug,
                "title" => 'Monthly Report',
                'menu_title' => "Monthly Report",
                'capability' => "manage_categories",
                'slug' => "gt_monthly_report",
                "callback" => ['\App\Reports\MonthlyReportController', 'show_report']
            ],
            [
                "parent_slug" => $parent_slug,
                "title" => 'Products Report',
                'menu_title' => "Products Report",
                'capability' => "manage_categories",
                'slug' => "gt_products_report",
                "callback" => ['\App\Reports\ProductsReportController', 'show_report']
            ],
            [
                "parent_slug" => $parent_slug,
                "title" => 'Commandes Freelance',
                'menu_title' => "Commandes Freelance",
                'capability' => "manage_categories",
                'slug' => "gt_freelance_report",
                "callback" => ['\App\Reports\FreeLanceController', 'show_report']
            ],
            [
                "parent_slug" => $parent_slug,
                "title" => 'TEst',
                'menu_title' => "TEst",
                'capability' => "manage_categories",
                'slug' => "gt_test_report",
                "callback" => ['\App\Reports\TestController', 'show_report']
            ]
        ];
    }

    private function getSettings()
    {
        return [
            [
                "group_name" => 'gt_admin_settings',
                "option_name" => "text_example",
                "callback" => [$this, 'settings_callback']
            ]
        ];
    }

    private function getSections()
    {
        return [
            [
                "id" => "gt_settings",
                "title" => "Plugin Settings",
                "callback" => [$this, 'sections_callback'],
                "page" => "gt_plugin_settings"
            ]
        ];
    }

    private function getFields()
    {
        return [
            [
                "id" => "text_example",
                "title" => "Field 1:",
                "callback" => [$this, 'field1_callback'],
                "page" => "gt_plugin_settings",
                "section" => "gt_settings",
                "args" => [
                    "label_for" => "text_example",
                    "class" => "form-control"
                ]
            ]
        ];
    }

    public function settings_callback($input)
    {
        //return back the input.

        //no sanitization needed
        return $input;
    }

    public function sections_callback()
    {
        echo "Glotelho Plugin General Settings";
    }

    public function field1_callback()
    {
        $oldValue = esc_attr(get_option("text_example"));

        echo '<input type="text" class="regula-text" '
            .' name="text_example" value="' . $oldValue . '" >';
    }



    public function load_page()
    {

        return $page;
    }
}

 ?>
