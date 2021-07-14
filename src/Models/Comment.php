<?php

namespace Botble\Comment\Models;

use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;

class Comment extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'comments';

    /**
     * @var array
     */
    protected $fillable = [
        'comment',
        'reference_id',
        'reference_type',
        'ip_address',
        'user_id',
        'status',
        'parent_id',
        'reply_count',

    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    protected $appends = [
        'time',
        'rep',
    ];

    protected $with = [
        'user',
//        'replies',
//        'likes',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(CommentUser::class, 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id', 'id');
    }

    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * @return mixed
     */
    public function getTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getRepAttribute()
    {
        return (int)$this->reply_count > 0 ? $this->replies()
            ->orderBy('created_at', 'DESC')
            ->paginate(2) : [];
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function (Comment $comment) {
            if ((int)$comment->parent_id !== 0) {
                $parent = Comment::where(['id' => $comment->parent_id])->first();
                $parent->reply_count = Comment::where(['parent_id' => $parent->id])->count();
                $parent->save();
            }
        });

        static::deleted(function (Comment $comment) {
            Comment::where(['parent_id' => $comment->id])->delete();
        });
    }
}
