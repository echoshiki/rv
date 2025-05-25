<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $openid
 * @property string|null $unionid
 * @property array<array-key, mixed>|null $raw_data 原始微信数据
 * @property string|null $nickname
 * @property string|null $avatar_url
 * @property int|null $gender
 * @property string|null $country
 * @property string|null $province
 * @property string|null $city
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $avatar
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\WechatUserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereRawData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereSessionKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereUnionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser withoutTrashed()
 * @mixin \Eloquent
 */
class WechatUser extends Model
{
    /** @use HasFactory<\Database\Factories\WechatUserFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'openid', 'unionid', 
        'nickname', 'avatar', 'user_id'
    ];

    protected $casts = [
        'raw_data' => 'array'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    // 访问器：微信头像处理
    public function getAvatarAttribute()
    {
        $avatar = $this->avatar ?? null;
        return $avatar ?: asset('images/wechat-default.png');
    }
}
