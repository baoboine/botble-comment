<?php

namespace Botble\Comment\Models;

use Botble\Base\Supports\Avatar;
use Botble\Base\Traits\EnumCastable;
use Botble\Media\Models\MediaFile;
use Illuminate\Container\Container;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\PersonalAccessTokenFactory;
use RvMedia;

class CommentUser extends Authenticatable
{
    use EnumCastable;

    protected $table = 'bb_comment_users';

    protected $accessToken;

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

    public function rating()
    {
        return $this->hasOne(CommentRating::class, 'user_id')->withDefault();
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

    /**
     * Create a new personal access token for the user.
     *
     * @param string $name
     * @param array $scopes
     */
    public function createToken($name, array $scopes = [])
    {
        return Container::getInstance()->make(PersonalAccessTokenFactory::class)->make(
            $this->getKey(), $name, $scopes
        );
    }

    /**
     * Get the current access token being used by the user.
     *
     * @return \Laravel\Passport\Token|null
     */
    public function token()
    {
        return $this->accessToken;
    }

    /**
     * Set the current access token for the user.
     *
     * @param  \Laravel\Passport\Token  $accessToken
     * @return $this
     */
    public function withAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

}
