<?php

namespace Botble\Comment\Http\Controllers\API;

use Botble\ACL\Traits\RegistersUsers;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Comment\Repositories\Interfaces\CommentUserInterface;
use Botble\Member\Models\Member;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    use RegistersUsers;

    /**
     * @var CommentUserInterface
     */
    protected $commentUserRepository;

    public function __construct(CommentUserInterface $commentUserRepository)
    {
        $this->commentUserRepository = $commentUserRepository;
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth(COMMENT_GUARD);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function register(Request $request, BaseHttpResponse $response)
    {
        $validate = $this->validator($request->input());

        if ($validate->fails()) {
            return $response
                ->setMessage($validate->getMessageBag())
                ->setError(true)
                ->toApiResponse();
        }

        event(new Registered($member = $this->create($request->input())));

        $this->guard()->login($member);
        return $this->registered($request, $member, $response)
            ?: $response->setNextUrl($this->redirectPath());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, array_merge([
            'name'       => 'required|max:255',
            'email'      => 'required|email|max:255|unique:bb_comment_users',
            'password'   => 'required|min:6|confirmed',
        ],
            setting('enable_captcha') && is_plugin_active('captcha') ? ['g-recaptcha-response' => 'required|captcha'] : []
        ));
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return Member
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function create(array $data)
    {
        return $this->commentUserRepository->create([
            'name'      => $data['name'],
            'email'      => $data['email'],
            'password'   => bcrypt($data['password']),
        ]);
    }

    public function registered(Request $request, $user, $response)
    {
        $token = auth(COMMENT_GUARD)->user()->createToken('Laravel Password Grant Client')->accessToken;

        return $response
            ->setData(compact('token'));
    }
}
