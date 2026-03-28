<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingsHelper
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return Setting::getValue($key, $default);
    }

    public static function all(): array
    {
        return Setting::getAll();
    }
}
