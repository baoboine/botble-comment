<div class="bb-comment-wrapper" id="bb-comment">
    <comment
        reference="{{ $reference }}"
        url="{{ route('comment.list') }}"
        post-url="{{ route('comment.post') }}"
        user-url="{{ route('comment.user') }}"
        delete-url="{{ route('comment.delete') }}"
        :logged-user="{{ json_encode($loggedUser) }}"
        check-current-user-api="{{ route('comment.current-user') }}"
        like-url="{{ route('comment.like') }}"
        change-avatar-url="{{ route('comment.update-avatar') }}"
        login-url="{{ route('public.comment.login') }}"
        logout-url="{{ route('comment.logout') }}"
        register-url="{{ route('public.comment.register') }}"
        recommend-url="{{ route('comment.recommend') }}"
        captcha="{{ setting('enable_captcha') && is_plugin_active('captcha') ? Captcha::display(['add-js' => false]) : '' }}"
    ></comment>
</div>
