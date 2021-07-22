<?php


namespace Botble\Comment\Supports;

use Botble\Comment\Repositories\Interfaces\CommentUserInterface;
use Illuminate\Http\Request;


class CloneUserToCommentUser
{
    protected $commentUser;

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function __construct()
    {
        $this->commentUser = app(CommentUserInterface::class);
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(Request $request)
    {
        if ($user = $request->user() ?: (config('auth.guards.member') && auth()->guard('member')->user())) {
            return $this->commentUser->createOrUpdate([
                'email'     => $user->email,
                'password'  => $user->password,
                'avatar_id' => $user->avatar_id,
                'user_type' => get_class($user),
                'name'      => join(' ', [$user->first_name, $user->last_name]),
            ], ['email' => $user->email]);
        }

        return false;
    }
}
