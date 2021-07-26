<?php

namespace Botble\Comment\Providers;

use Botble\Comment\Models\Comment;
use Botble\Comment\Models\CommentLike;
use Botble\Comment\Models\CommentRating;
use Botble\Comment\Models\CommentRecommend;
use Botble\Comment\Models\CommentUser;
use Botble\Comment\Repositories\Caches\CommentLikeCacheDecorator;
use Botble\Comment\Repositories\Caches\CommentRatingCacheDecorator;
use Botble\Comment\Repositories\Caches\CommentRecommendCacheDecorator;
use Botble\Comment\Repositories\Caches\CommentUserCacheDecorator;
use Botble\Comment\Repositories\Eloquent\CommentLikeRepository;
use Botble\Comment\Repositories\Eloquent\CommentRatingRepository;
use Botble\Comment\Repositories\Eloquent\CommentRecommendRepository;
use Botble\Comment\Repositories\Eloquent\CommentUserRepository;
use Botble\Comment\Repositories\Interfaces\CommentLikeInterface;
use Botble\Comment\Repositories\Interfaces\CommentRatingInterface;
use Botble\Comment\Repositories\Interfaces\CommentRecommendInterface;
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
use EmailHandler;

class CommentServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {

        Helper::autoload(__DIR__ . '/../../helpers');


        $this->app->bind(CommentInterface::class, function () {
            return new CommentCacheDecorator(new CommentRepository(new Comment));
        });

        $this->app->bind(CommentUserInterface::class, function () {
            return new CommentUserCacheDecorator(new CommentUserRepository(new CommentUser));
        });

        $this->app->bind(CommentLikeInterface::class, function() {
            return new CommentLikeCacheDecorator(new CommentLikeRepository(new CommentLike));
        });

        $this->app->bind(CommentRecommendInterface::class, function() {
            return new CommentRecommendCacheDecorator(new CommentRecommendRepository(new CommentRecommend));
        });

        $this->app->bind(CommentRatingInterface::class, function() {
            return new CommentRatingCacheDecorator(new CommentRatingRepository(new CommentRating));
        });

        config([
            'auth.guards.'.COMMENT_GUARD     => [
                'driver'   => 'session',
                'provider' => COMMENT_GUARD,
            ],
            'auth.providers.'.COMMENT_GUARD => [
                'driver' => 'eloquent',
                'model'  => CommentUser::class,
            ],
            'auth.guards.comment-api' => [
                'driver'   => 'passport',
                'provider' => COMMENT_GUARD,
            ],
        ]);
        $this->configureRateLimiting();
    }

    public function boot()
    {
        $this->setNamespace('plugins/comment')
            ->loadAndPublishConfigurations(['permissions', 'email'])
            ->loadMigrations()
            ->publishAssets()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web', has_passport() ? 'api' : 'ajax']);

        $this->app->register(EventServiceProvider::class);

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
                    'icon'        => 'fa fa-comment',
                    'url'         => route('comment.index'),
                    'permissions' => ['comment.index'],
                ])

                ->registerItem([
                    'id'          => 'cms-plugins-comment-comment',
                    'priority'    => 1,
                    'parent_id'   => 'cms-plugins-comment',
                    'name'        => 'plugins/comment::comment.name',
                    'icon'        => null,
                    'url'         => route('comment.index'),
                    'permissions' => ['comment.index'],
                ])

                ->registerItem([
                    'id'          => 'cms-plugins-comment-user',
                    'priority'    => 2,
                    'parent_id'   => 'cms-plugins-comment',
                    'name'        => 'plugins/comment::comment-user.name',
                    'icon'        => null,
                    'url'         => route('comment-user.index'),
                    'permissions' => ['comment-user.index'],
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

                EmailHandler::addTemplateSettings(COMMENT_MODULE_SCREEN_NAME, config('plugins.comment.email', []));
        });
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('comment', function() {
            return Limit::perMinute(20);
        });
    }
}
