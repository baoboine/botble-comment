<?php


namespace Botble\Comment\Supports;

use Botble\Comment\Repositories\Interfaces\CommentUserInterface;
use Illuminate\Http\Request;
use Botble\ACL\Models\User;


class CloneUserToCommentUser
{
    protected $commentUser;

    public function __construct(CommentUserInterface $commentUser)
    {
        $this->commentUser = $commentUser;
    }

    public function handle(Request $request)
    {
        if ($user = $request->user()) {
            return $this->commentUser->createOrUpdate([
                'name'  => join(' ', [$user->first_name, $user->last_name]),
                'email' => $user->email,
                'password' => $user->password,
                'avatar_id' => $user->avatar_id,
                'user_type' => User::class,
            ], ['email' => $user->email]);
        }

        return false;
    }
}
