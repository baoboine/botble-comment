<?php

namespace Botble\Comment\Supports;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Guards\TokenGuard;
use Laravel\Passport\PassportUserProvider;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\ResourceServer;

class CheckMemberCredentials
{
    protected $app;

    protected $provider = 'members';

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(Request $request)
    {
        $user = (new TokenGuard(
            $this->app->make(ResourceServer::class),
            new PassportUserProvider(Auth::createUserProvider($this->provider), $this->provider),
            $this->app->make(TokenRepository::class),
            $this->app->make(ClientRepository::class),
            $this->app->make('encrypter')
        ))->user($request);

        app('auth')->guard('member')->setUser($user);
        return $user;
    }
}
