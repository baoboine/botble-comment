<?php

namespace Botble\Comment\Supports;

use Artisan;
use Botble\Comment\Supports\Traits\SiteApi;
use Botble\PluginManagement\Services\PluginService;
use File;
use Http;
use Arr;
use GuzzleHttp\Exception\RequestException;
use ZipArchive;

class Updater
{
    use SiteApi;

    public static $stepMessage = [
        '1' => 'Download ZIP file',
        '2' => 'Unzipping Package',
        '3' => 'Copying Files',
        '4' => 'Deleting Unused files',
        '5' => 'Running Migrations and publish Assets'
    ];

    public static function checkForUpdate()
    {
        $data = null;
        $url = 'check/latest/?type=update';

        $response = static::getRemote($url, ['timeout' => 100, 'track_redirects' => true]);

        if ($response && ($response->getStatusCode() == 200)) {
            $data = $response->getBody()->getContents();
        }

        $data = json_decode($data);

        return [
            'has'       => !!version_compare($data->data->version, comment_plugin_version(), '>'),
            'version'   => $data->data->version,
        ];
    }

    public static function download($is_cmd = 0)
    {
        $data = null;
        $path = null;

        $url = 'file/?type=update&is_dev=1&is_cmd='.$is_cmd;

        $response = static::getRemote($url, ['timeout' => 100, 'track_redirects' => true]);

        // Exception
        if ($response instanceof RequestException) {
            return [
                'success' => false,
                'error' => 'Download Exception',
                'data' => [
                    'path' => $path,
                ],
            ];
        }

        if ($response && ($response->getStatusCode() == 200)) {
            $data = $response->getBody()->getContents();
        }

        $data = json_decode($data);

        $zipData = Http::get($data->data->file);

        // Create temp directory
        $temp_dir = storage_path('app/temp-'.md5(mt_rand()));

        if (! File::isDirectory($temp_dir)) {
            File::makeDirectory($temp_dir);
        }

        $zip_file_path = $temp_dir.'/upload.zip';

        // Add content to the Zip file
        $uploaded = is_int(file_put_contents($zip_file_path, $zipData));

        if (! $uploaded) {
            return false;
        }

        return $zip_file_path;
    }

    public static function unzip($zip_file_path)
    {
        if (! file_exists($zip_file_path)) {
            throw new \Exception('Zip file not found');
        }

        $temp_extract_dir = storage_path('app/temp2-'.md5(mt_rand()));

        if (! File::isDirectory($temp_extract_dir)) {
            File::makeDirectory($temp_extract_dir);
        }
        // Unzip the file
        $zip = new ZipArchive();

        if ($zip->open($zip_file_path)) {
            $zip->extractTo($temp_extract_dir);
        }

        $zip->close();

        // Delete zip file
        File::delete($zip_file_path);

        return $temp_extract_dir;
    }

    public static function copyFiles($temp_extract_dir)
    {
        $pluginFolder = Arr::first(scan_folder($temp_extract_dir));
        if (! File::copyDirectory($temp_extract_dir. '/'. $pluginFolder, plugin_path('comment'))) {
            return false;
        }

        // Delete temp directory
        File::deleteDirectory($temp_extract_dir);

        return true;
    }

    public static function deleteFiles($files)
    {
        if ($files && is_array($files)) {
            foreach ($files as $file) {
                \File::delete(base_path($file));
            }
        }


        return true;
    }

    public static function updateAssets()
    {
        app(PluginService::class)->publishAssets('comment');
        app('migrator')->run(plugin_path('comment/database/migrations'));

        return true;
    }
}
