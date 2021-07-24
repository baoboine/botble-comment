<?php


namespace Botble\Comment\Http\Controllers;


use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Comment\Supports\Updater;

class UpdateController extends BaseController
{

    protected $response;

    public function __construct(BaseHttpResponse $response)
    {
        $this->response = $response;
    }

    public function checkVersion()
    {
        set_time_limit(600); // 10 minutes

        $json = Updater::checkForUpdate();

        return $this->response->setData($json);
    }

    /**
     * @throws \Exception
     */
    public function download()
    {
        $zipPath = Updater::download(false);
        $directory = Updater::unzip($zipPath);
        $ok = Updater::copyFiles($directory);

        Updater::updateAssets();


        return $this->response->setData(compact('ok'));
    }
}
