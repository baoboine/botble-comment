import UpdateVersionService from "./settings/UpdateVersionService";

$(function() {
    let $commentEnableCheckbox = $('#comment-enable');
    let $areaCommentSetting = $('#show-comments-setting');
    let $updaterVersion = $('#comment-plugin-updater');

    if ($commentEnableCheckbox.is(':checked')) {
        $areaCommentSetting.show();
    }

    $commentEnableCheckbox.on('change', e => {
        if (e.target.checked) {
            $areaCommentSetting.show();
        } else {
            $areaCommentSetting.hide();
        }
    });

    if ($updaterVersion.length) {

        new UpdateVersionService($updaterVersion);

    }
});
