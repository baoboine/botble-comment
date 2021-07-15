<?php


namespace Botble\Comment\Models;

use Botble\Base\Supports\Avatar;
use Botble\Media\Models\MediaFile;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use RvMedia;


class CommentUser extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'bb_comment_users';

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
        'password',
        'user_type',
        'avatar_id',

    ];

    protected $appends = [
        'avatar_url'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function avatar()
    {
        return $this->belongsTo(MediaFile::class)->withDefault();
    }

    /**
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getAvatarUrlAttribute()
    {
        $src = $this->avatar->url ? RvMedia::url($this->avatar->url) : (new Avatar)
            ->setShape('square')
            ->create($this->name)->toBase64();

        if (!is_string($src)) {
            return $src->encoded;
        }
        return $src;
    }

}
