<?php namespace Marin\SteamAuth\Models;

use Model;

/**
 * SteamConfig Model
 */
class SteamConfig extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'marin_steamauth_config';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';
}
