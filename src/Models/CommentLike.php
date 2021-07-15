<?php


namespace Botble\Comment\Models;


use Botble\Base\Models\BaseModel;

class CommentLike extends BaseModel
{
    protected $table = 'bb_comment_likes';

    protected $fillable = [
        'user_id',
        'comment_id',
    ];

    protected static function updateCountLike(CommentLike $like) {
        $comment = Comment::where(['id' => $like->comment_id])->first();

        $comment->like_count = CommentLike::where(['comment_id' => $like->comment_id])->count();
        $comment->save();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function(CommentLike $like) {
            static::updateCountLike($like);
        });

        static::deleted(function(CommentLike $like) {
            static::updateCountLike($like);
        });
    }
}
