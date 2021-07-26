<?php


namespace Botble\Comment\Models;


use Botble\Base\Models\BaseModel;

class CommentRating extends BaseModel
{
    protected $table = 'bb_comment_ratings';

    protected $fillable = [
        'reference_id',
        'reference_type',
        'rating',
        'user_id',
    ];

}
