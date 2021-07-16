<?php


namespace Botble\Comment\Providers;

use Botble\Comment\Events\NewCommentEvent;
use Botble\Comment\Listeners\NewCommentListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        NewCommentEvent::class => [
            NewCommentListener::class,
        ],
    ];
}
