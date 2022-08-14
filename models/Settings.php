<?php namespace Baoweb\Articles\Models;

use Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'baoweb_articles_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';
}
