<?php namespace Marin\SteamAuth;

use Backend;
use System\Classes\PluginBase;

/**
 * SteamAuth Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'SteamAuth',
            'description' => 'Provides Steam Authentication and retrieves user info',
            'author'      => 'Marin',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [];
    }

    public function registerSettings()
    {
        return [
            'location' => [
                'label'       => 'Steam API',
                'description' => 'Steam API configuration',
                'category'    => 'Steam',
                'icon'        => 'icon-globe',
                'class'       => 'Marin\SteamAuth\Models\SteamConfig',
                'order'       => 500,
                'keywords'    => 'steam api configuration'
            ]
        ];
    }
}
