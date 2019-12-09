<?php
namespace App\Api;


/**
 *
 */
class SettingsApi
{

    public $pages = [];

    public $subpages = [];

    public $settings = [];

    public $sections = [];

    public $fields = [];

    public function register()
    {

        if(!empty($this->pages))
        {
            add_action('admin_menu', [$this, 'add_menu_page']);
        }

        // var_dump($this->settings); die();

        if(!empty($this->settings))
        {
            add_action("admin_init", [$this, 'registerCustomFields']);
        }

    }

    public function addPages(array $pages)
    {
        $this->pages = $pages;

        return $this;
    }

    public function addSubPages(array $subpages)
    {
        $this->subpages = $subpages;

        return $this;
    }

    public function add_menu_page()
    {
        foreach($this->pages as $page)
        {
            add_menu_page($page['title'], $page['menu_title'],
                $page['capability'], $page['slug'], $page['callback'],
                $page['icon'], $page['position']);
        }

        foreach($this->subpages as $page)
        {
            add_submenu_page( $page['parent_slug'], $page['title'], $page['menu_title'],
                $page['capability'], $page['slug'], $page['callback'] );
        }
    }

    public function setSettings($settings)
    {
        $this->settings = $settings;

        return $this;
    }

    public function setFields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    public function setSections($sections)
    {
        $this->sections = $sections;

        return $this;
    }

    public function registerCustomFields()
    {
        foreach($this->settings as $setting)
        {
            register_setting($setting['group_name'], $setting['option_name'],
                ( isset($setting['callback']) ? $setting['callback'] : '' ));
        }

        foreach($this->sections as $section)
        {
            add_settings_section( $section["id"], $section["title"],
                (isset($section['callback']) ? $section['callback'] : ''),
                $section['page'] );
        }

        foreach($this->fields as $field)
        {
            add_settings_field( $field['id'], $field['title'],
                (isset($field['callback']) ? $field['callback'] : ' '),
                $field['page'], $field['section'], $field['args'] );
        }
    }

}

?>
