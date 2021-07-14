<?php


namespace Botble\Comment\Models;


use Botble\Base\Models\BaseModel;

class CommentLike extends BaseModel
{

    protected $fillable = [
        'user_id',
        'comment_id',
    ];
}
