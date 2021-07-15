<?php

if (!function_exists('has_member'))
{
    function has_member(): bool
    {
        if (config()->has('has_member')) {
            return config('has_member');
        }

        $config = (function() {
            try {
                return is_plugin_active('member');
            } catch(\Illuminate\Contracts\Filesystem\FileNotFoundException $exception) {
                return false;
            }
        })();

        config([
            'has_member' => $config
        ]);

        return $config;
    }
}
