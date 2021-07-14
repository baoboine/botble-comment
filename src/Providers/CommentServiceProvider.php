<?php

namespace Botble\Comment\Providers;

use Botble\Comment\Models\Comment;
use Botble\Comment\Models\CommentUser;
use Botble\Comment\Repositories\Caches\CommentUserCacheDecorator;
use Botble\Comment\Repositories\Eloquent\CommentUserRepository;
use Botble\Comment\Repositories\Interfaces\CommentUserInterface;
use Illuminate\Support\ServiceProvider;
use Botble\Comment\Repositories\Caches\CommentCacheDecorator;
use Botble\Comment\Repositories\Eloquent\CommentRepository;
use Botble\Comment\Repositories\Interfaces\CommentInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

class CommentServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(CommentInterface::class, function () {
            return new CommentCacheDecorator(new CommentRepository(new Comment));
        });

        $this->app->bind(CommentUserInterface::class, function () {
            return new CommentUserCacheDecorator(new CommentUserRepository(new CommentUser));
        });

        if (!is_plugin_active('member')) {
            config([
                'auth.guards.member'     => [
                    'driver'   => 'session',
                    'provider' => 'members',
                ],
                'auth.providers.members' => [
                    'driver' => 'eloquent',
                    'model'  => CommentUser::class,
                ],
                'auth.guards.member-api' => [
                    'driver'   => 'passport',
                    'provider' => 'members',
                ],
            ]);
        }

        Helper::autoload(__DIR__ . '/../../helpers');
        $this->configureRateLimiting();
    }

    public function boot()
    {
        $this->setNamespace('plugins/comment')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->publishAssets()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web', 'api']);

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });

        Event::listen(RouteMatched::class, function () {

            dashboard_menu()
                ->registerItem([
                    'id'          => 'cms-plugins-comment',
                    'priority'    => 5,
                    'parent_id'   => null,
                    'name'        => 'plugins/comment::comment.name',
                    'icon'        => 'fa fa-list',
                    'url'         => route('comment.index'),
                    'permissions' => ['comment.index'],
                ])

                ->registerItem([
                    'id'          => 'cms-plugins-comment-setting',
                    'priority'    => 5,
                    'parent_id'   => 'cms-core-settings',
                    'name'        => 'plugins/comment::comment.name',
                    'icon'        => null,
                    'url'         => route('comment.setting'),
                    'permissions' => ['setting.options'],
                ]);
        });
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('comment', function() {
            return Limit::perMinute(20);
        });
    }
}
