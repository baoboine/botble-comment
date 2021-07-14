<?php


namespace Botble\Comment\Models;


use Botble\Base\Models\BaseModel;

class CommentLike extends BaseModel
{

    protected $fillable = [
        'user_id',
        'comment_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function(CommentLike $like) {
            $comment = Comment::where(['id' => $like->comment_id])->first();

            $comment->like_count = CommentLike::where(['comment_id' => $like->comment_id])->count();
            $comment->save();
        });
    }
}
