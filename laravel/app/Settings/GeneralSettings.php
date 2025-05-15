<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{

    public string $title;
    public ?string $description;
    public ?string $keywords;
    public ?string $logo;
    public ?string $default_cover;
    public ?string $default_avatar;
    public ?string $email;
    public ?string $phone;
    public ?string $address;
    public ?string $copyright;
    public ?string $icp;

    public static function group(): string
    {
        return 'general';
    }
}