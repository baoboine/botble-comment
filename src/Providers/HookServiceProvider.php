<?php


namespace Botble\Comment\Providers;

use Illuminate\Support\ServiceProvider;
use Theme;

class HookServiceProvider extends ServiceProvider
{

    protected $currentReference;

    public function boot()
    {
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
     * @return string|null
     */
    protected function getReference(): ?string
    {
        if ($object = $this->currentReference) {
            return base64_encode(json_encode([
                'reference_type'    => get_class($object),
                'reference_id'      => $object->id,
            ]));
        }
        return NULL;
    }

    /**
     *
     */
    protected function loadAssets()
    {
        Theme::asset()
            ->container('footer')
            ->usePath(false)
            ->add('bb-comment', 'vendor/core/plugins/comment/js/comment.js', ['jquery']);

        Theme::asset()
            ->usePath(false)
            ->add('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css')
            ->add('bb-comment-css', 'vendor/core/plugins/comment/css/comment.css');
    }
}
