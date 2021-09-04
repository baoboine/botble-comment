<?php

namespace Botble\Comment\Http\Controllers\API;

use Botble\ACL\Traits\AuthenticatesUsers;
use Botble\ACL\Traits\LogoutGuardTrait;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Comment\Models\CommentUser;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    protected $response;

    use AuthenticatesUsers, LogoutGuardTrait {
        AuthenticatesUsers::attemptLogin as baseAttemptLogin;
        AuthenticatesUsers::sendLoginResponse as sendBaseLoginResponse;
    }

    public function __construct(BaseHttpResponse $response)
    {
        $this->response = $response;
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth(COMMENT_GUARD);
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     * @throws ValidationException
     */
    protected function attemptLogin(Request $request)
    {
        if ($this->guard()->validate($this->credentials($request))) {
            return $this->baseAttemptLogin($request);
        }

        return false;
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $data = $request->only($this->username(), 'password');
        return array_merge($data, ['user_type' => CommentUser::class]);
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return BaseHttpResponse
     */
    public function logout(Request $request, BaseHttpResponse $response)
    {
        $request->user()->token()->revoke();

        return $response
            ->setMessage(__('You have been successfully logged out!'));
    }

    public function sendLoginResponse(Request $request)
    {
        if ($request->ajax()) {
            $token = auth(COMMENT_GUARD)->user()->createToken('Laravel Password Grant Client')->accessToken;

            return $this->response->setData([
                'token' => $token,
            ]);
        }

        return $this->sendBaseLoginResponse($request);
    }
}
