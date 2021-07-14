<?php


namespace Botble\Comment\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;


class CommentUser extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'comment_users';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'name',
        'email',
        'password'
    ];

}
