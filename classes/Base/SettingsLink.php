<?php
/**
*  Setting the settings link for this plugin
**/

namespace App\Base;

/**
 *
 */
class SettingsLink
{
    protected $plugin;

    public function __construct()
    {
        $this->plugin = PLUGIN_NAME;
    }

    public function register()
    {
        add_filter("plugin_action_links_".PLUGIN_BASENAME, [$this, 'settings_link']);
    }

    public function settings_link($links)
    {
        $settings_link = '<a href="admin.php?page=gt_plugin_settings">Settings</a>';

        //add it to the array of links
        array_push($links, $settings_link);

        return $links;
    }
}


 ?>
