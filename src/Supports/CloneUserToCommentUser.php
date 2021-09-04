<?php


namespace Botble\Comment\Supports;

use Botble\Comment\Repositories\Interfaces\CommentUserInterface;
use Exception;
use Illuminate\Http\Request;


class CloneUserToCommentUser
{
    /**
     * @var CommentUserInterface
     */
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
    public function handle(Request $request, $guard = null)
    {
        try {
            $user = auth($guard)->check() ? auth($guard)->user() : null;
        } catch (Exception $ex) {
            return false;
        }

        if ($user) {
            $commentUser = $this->commentUser->getFirstBy([
                'email'     => $user->email,
                'user_type' => get_class($user),
            ]);
            if (!$commentUser) {
                $commentUser = $this->commentUser->createOrUpdate([
                    'email'     => $user->email,
                    'password'  => $user->password,
                    'avatar_id' => $user->avatar_id,
                    'user_type' => get_class($user),
                    'name'      => join(' ', [$user->first_name, $user->last_name]) ?: $user->name ?: $user->email,
                ]);
            }

            return $commentUser;
        }

        return false;
    }
}
