<?php


namespace Botble\Comment\Http\Controllers;


use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Comment\Supports\Updater;
use Botble\Setting\Supports\SettingStore;
use Illuminate\Http\Request;

class UpdateController extends BaseController
{

    protected $response;

    protected $prefixSetting = 'comment_plugin_update_progress';

    protected $settings;

    public function __construct(BaseHttpResponse $response)
    {
        $this->response = $response;
    }

    public function checkVersion(SettingStore $settingStore): BaseHttpResponse
    {
        set_time_limit(600); // 10 minutes

        $json = Updater::checkForUpdate();

        if ($json['has']) {
            $this->resetSettingStep($settingStore);
        }

        return $this->response->setData($json);
    }

    /**
     * @throws \Exception
     */
    public function download(SettingStore $settingStore, Request $request)
    {
        [$step, $data] = $this->getCurrentStep();

        $file = null;

        if ($step === 1) {
            $file = Updater::download(false);
            $this->saveCurrentStep($settingStore, $file);
        } else if ($step === 2) {
            $file = Updater::unzip($data);
            $this->saveCurrentStep($settingStore, $file);
        } else if ($step === 3) {
            $ok = Updater::copyFiles($data);
            $this->saveCurrentStep($settingStore, $ok);
        } else if ($step === 4) {
            Updater::updateAssets();
            $this->saveCurrentStep($settingStore, 1);
        } else if ($step === 5) {
            Updater::deleteFiles($request->input('files'));
            $this->resetSettingStep($settingStore);

            return $this->response->setData(['ok' => true]);
        }

        $message = Updater::$stepMessage[$step + 1];

        return $this->response->setMessage($message)->setData(compact('file'));
    }

    protected function getCurrentStep(): array
    {
        if (setting($this->prefixSetting)) {
            $settings = json_decode(setting($this->prefixSetting));

            $this->settings = $settings;

            return [
                (int)$settings->step,
                $settings->data
            ];
        }

        return [
            1,
            []
        ];
    }

    /**
     * @param SettingStore $settingStore
     * @param $data
     */
    protected function saveCurrentStep(SettingStore $settingStore, $data)
    {
        $settingStore->set($this->prefixSetting, json_encode([
            'step'  => $this->settings ? $this->settings->step + 1 : 2,
            'data'  => $data,
        ]));
        $settingStore->save();
    }

    protected function resetSettingStep(SettingStore $settingStore)
    {
        $settingStore
            ->forget($this->prefixSetting)
            ->save();
    }
}
