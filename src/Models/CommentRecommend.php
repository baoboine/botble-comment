<?php


namespace Botble\Comment\Models;


use Botble\Base\Models\BaseModel;

class CommentRecommend extends BaseModel
{
    protected $table = 'bb_comment_recommends';

    protected $fillable = [
        'reference_id',
        'reference_type',
        'user_id',
    ];

}
