<?php


namespace Botble\Comment\Providers;

use Botble\ACL\Models\User;
use Botble\Blog\Models\Post;
use Botble\Comment\Repositories\Interfaces\CommentInterface;
use Botble\Comment\Repositories\Interfaces\CommentRatingInterface;
use RvMedia;
use Botble\Member\Models\Member;
use Illuminate\Support\ServiceProvider;
use MacroableModels;
use Theme;
use Html;

class HookServiceProvider extends ServiceProvider
{

    protected $currentReference;

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function boot()
    {
        $this->registerAttribute();

        add_shortcode('comment', 'Comment', 'Comment for this article', [$this, 'renderComment']);
        add_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, [$this, 'storageCurrentReference'], 100, 2);
        add_filter(BASE_FILTER_APPEND_MENU_NAME, [$this, 'getUnreadCount'], 210, 2);
    }

    /**
     * Render comment view section
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function renderComment()
    {
        if (!setting('comment_enable')) {
            return null;
        }

        $this->loadAssets();

        $reference = $this->getReference();
        $loggedUser = auth()->user() ? request()->user()->only(['id', 'email']) : ['id' => 0];

        add_filter(THEME_FRONT_HEADER, function ($html) {
            $this->addSchemas($html);

            return $html . view('plugins/comment::partials.trans');
        }, 15);

        return $reference ? view('plugins/comment::short-codes.comment', compact('reference', 'loggedUser')) : null;
    }

    /**
     * @param $screen
     * @param $object
     */
    public function storageCurrentReference($screen, $object)
    {
        $this->currentReference = $object;
        $menuEnables = json_decode(setting('comment_menu_enable', '[]'), true);

        if (setting('comment_enable') && in_array(get_class($object), $menuEnables)) {
            if (strpos($object->content, '[comment') === FALSE) {
                $object->content.= '[comment][/comment]';
            }
        }
    }

    /**
     * @return string|array
     */
    protected function getReference($isBase64 = true)
    {
        if ($object = $this->currentReference) {
            $reference = [
                'reference_type'    => get_class($object),
                'reference_id'      => $object->id,
                'author'            => [
                    'id'    => $this->currentReference->user_id ?: $this->currentReference->author_id,
                    'type'  => $this->currentReference->user_type ?: $this->currentReference->author_type ?: User::class,
                ]
            ];
            return $isBase64 ? base64_encode(json_encode($reference)) : $reference;
        }

        return null;
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function loadAssets()
    {
        Theme::asset()
            ->container('footer')
            ->usePath(false)
            ->add('bb-comment', 'vendor/core/plugins/comment/js/comment.js', ['jquery'], [], comment_plugin_version());

        Theme::asset()
            ->usePath(false)
            ->add('font-awesome-5', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css')
            ->add('bb-comment-css', 'vendor/core/plugins/comment/css/comment.css', [], [], comment_plugin_version());

        if (setting('enable_captcha') && is_plugin_active('captcha')) {
            Theme::asset()
                ->container('footer')
                ->usePath(false)
                ->add('google-recaptcha', 'https://www.google.com/recaptcha/api.js?hl='. app()->getLocale());
        }
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function registerAttribute()
    {
        if (has_member()) {
            MacroableModels::addMacro(Member::class, 'getNameAttribute', function() {
                return $this->first_name.' '. $this->last_name;
            });
        }
    }

    public function getUnreadCount($index, $menuId)
    {
        if ($menuId == 'cms-plugins-comment') {
            $unread = app(CommentInterface::class)->count([
                ['id', '>', setting('admin-comment_latest_viewed_id', 0)]
            ]);

            if ($unread > 0) {
                return Html::tag('span', (string)$unread, ['class' => 'badge badge-success'])->toHtml();
            }
        }

        return $index;
    }

    protected function addSchemas(&$html)
    {

        if (!setting('plugin_comment_rating', true)) return;

        $schemaJson = array (
            '@context' => 'http://schema.org',
            '@type' => 'NewsArticle',
        );

        $ratingData = app(CommentRatingInterface::class)->getRatingOfArticle($this->getReference(false), null);

        if ($this->currentReference && get_class($this->currentReference) === Post::class) {
            $post = $this->currentReference;
            $category = $post->categories()->first();

            if ($category) {
                $schemaJson['category'] = $category->name;
            }

            $schemaJson = array_merge($schemaJson, [
                'url' => $post->url,
                'description' => $post->description,
                'name' => $post->name,
                'image' => RvMedia::getImageUrl($post->image),
            ]);

            if ($ratingData['count'] > 0) {
                $schemaJson['aggregateRating'] = array (
                    '@type' => 'AggregateRating',
                    'ratingValue' => $ratingData['rating'],
                    'reviewCount' => $ratingData['count'],
                );
            }

            $html .= '<script type="application/ld+json">'. json_encode($schemaJson) .'</script>';

        }
    }
}
