<?php

namespace Botble\Comment\Supports\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

// Implementation taken from Akaunting - https://github.com/akaunting/akaunting
trait SiteApi
{
    protected static function getRemote($url, $data = [])
    {
        $base = 'https://baoboi.site/api/v1/download/';

        $client = new Client(['verify' => false, 'base_uri' => $base]);

        $headers['headers'] = [
            'Accept'    => 'application/json',
            'Referer'   => url('/'),
            'version'   => comment_plugin_version(),
            'X-Requested-With'  => 'XMLHttpRequest'
        ];

        $data['http_errors'] = false;

        $data = array_merge($data, $headers);

        try {
            $result = $client->get($url, $data);
        } catch (RequestException | GuzzleException $e) {
            $result = $e;
        }

        return $result;
    }
}
