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
        $name = PLUGIN_BASENAME;

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
                "title" => 'Glotelho Settings',
                'menu_title' => "Glotelho Settings",
                'capability' => "manage_options",
                'slug' => "gt_plugin_settings",
                "callback" => ['\App\Base\PagesController', 'admin_dashboard'],
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
                "title" => 'Regions',
                'menu_title' => "Regions",
                'capability' => "manage_options",
                'slug' => "gt_regions_settings",
                "callback" => ['\App\Base\PagesController', 'regions']
            ],
            [
                "parent_slug" => $parent_slug,
                "title" => 'Towns',
                'menu_title' => "Towns",
                'capability' => "manage_options",
                'slug' => "gt_towns_settings",
                "callback" => ['\App\Base\PagesController', 'towns']
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
