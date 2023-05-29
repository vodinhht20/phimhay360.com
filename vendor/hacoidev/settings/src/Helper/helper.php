<?php

use Backpack\Settings\app\Models\Setting;

if (!function_exists('setting')) {
    function setting($key, $fallback = null)
    {
        try {
            $setting = Setting::fromCache()->find($key);

            if (is_null($setting)) return $fallback;

            return $setting->value;
        } catch (\Exception $e) {
            return $fallback;
        }
    }
}
