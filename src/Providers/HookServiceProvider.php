<?php


namespace Botble\Comment\Providers;

use Botble\ACL\Models\User;
use Botble\Member\Models\Member;
use Illuminate\Support\ServiceProvider;
use Theme;

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
    }

    /**
     * Render comment view section
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function renderComment()
    {
        if (!setting('comment_enable')) return null;

        $this->loadAssets();

        $reference = $this->getReference();
        $loggedUser = auth()->user() ? request()->user()->only(['id', 'email']) : ['id' => 0];

        add_filter(THEME_FRONT_HEADER, function ($html) {
            return $html . view('plugins/comment::partials.trans');
        }, 15);

        return $reference ? view('plugins/comment::short-codes.comment', compact('reference', 'loggedUser')) : null;
    }

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
     * @return string
     */
    protected function getReference(): ?string
    {
        if ($object = $this->currentReference) {
            return base64_encode(json_encode([
                'reference_type'    => get_class($object),
                'reference_id'      => $object->id,
                'author'            => [
                    'id'    => $this->currentReference->user_id ?: $this->currentReference->author_id,
                    'type'  => $this->currentReference->user_type ?: $this->currentReference->author_type ?: User::class,
                ]
            ]));
        }

        return null;
    }

    /**
     *
     */
    protected function loadAssets()
    {
        Theme::asset()
            ->container('footer')
            ->usePath(false)
            ->add('bb-comment', 'vendor/core/plugins/comment/js/comment.js', ['jquery'], [], '2.0');

        Theme::asset()
            ->usePath(false)
            ->add('font-awesome-5', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css')
            ->add('bb-comment-css', 'vendor/core/plugins/comment/css/comment.css');
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function registerAttribute()
    {
        if (has_member()) {
            \MacroableModels::addMacro(Member::class, 'getNameAttribute', function() {
                return $this->first_name.' '. $this->last_name;
            });
        }
    }
}
