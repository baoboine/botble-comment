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
    ></comment>
</div>
