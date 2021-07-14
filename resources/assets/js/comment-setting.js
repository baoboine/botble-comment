$(function() {
    let $commentEnableCheckbox = $('#comment-enable');
    let $areaCommentSetting = $('#show-comments-setting');

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
})
