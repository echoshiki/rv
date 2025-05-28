<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 引入 RBAC 权限管理
use Spatie\Permission\Traits\HasRoles;

// WechatUser 模型
use App\Models\WechatUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// 生产环境必须实现方法
use Filament\Models\Contracts\FilamentUser;

use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $phone
 * @property string|null $phone_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $avatar_url
 * @property-read mixed $nickname
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read WechatUser|null $wechat_users
 * @method static Builder<static>|User active()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User inactive()
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User permission($permissions, $without = false)
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User recentlyRegistered(int $days = 7)
 * @method static Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User wherePhone($value)
 * @method static Builder<static>|User wherePhoneVerifiedAt($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User withPermission(string $permission)
 * @method static Builder<static>|User withRole(string $role)
 * @method static Builder<static>|User withWechatUserOpenid(string $openid)
 * @method static Builder<static>|User withoutPermission($permissions)
 * @method static Builder<static>|User withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'phone_verified_at',
        'level',
        'points',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * 确定用户是否可以访问Filament面板
     * 生产环境必须要实现的方法
     */
    public function canAccessPanel($panel): bool
    {
        // 基于角色、权限或其他逻辑决定
        return $this->isAdmin(); // 或其他条件
    }


    /**
     * 关联微信用户
     */
    public function wechat_users(): HasOne
    {
        return $this->hasOne(WechatUser::class);
    }

    /**
     * 用户添加的所有爱车
     */
    public function my_cars(): HasMany
    {
        return $this->hasMany(MyCar::class);
    }

    /**
     * 访问器：优先使用微信昵称
     */
    public function getNicknameAttribute()
    {
        return optional($this->wechat)->nickname ?? $this->name;
    }

    /**
     * 访问器：统一头像获取
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) return $this->avatar;
        // 默认指向 public/images/default-avatar.png
        return optional($this->wechat)->avatar ?? '';
    }

    /**
     * 根据微信 openid 查询关联的用户
     */
    public function scopeWithWechatUserOpenid($query, string $openid)
    {
        return $query->whereHas('wechat_users', function ($q) use ($openid) {
            $q->where('openid', $openid);
        });
    }

    /**
     * 查询活跃用户
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * 查询非活跃用户
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->whereNull('email_verified_at');
    }

    /**
     * 查询最近注册的用户
     */
    public function scopeRecentlyRegistered(Builder $query, int $days = 7): Builder
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * 查询指定角色的用户
     */
    public function scopeWithRole(Builder $query, string $role): Builder
    {
        return $query->role($role);
    }

    /**
     * 查询有指定权限的用户
     */
    public function scopeWithPermission(Builder $query, string $permission): Builder
    {
        return $query->permission($permission);
    }

    /**
     * 检查用户是否激活
     */
    public function isActive(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * 检查用户是否管理员
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('超级管理员') || $this->hasRole('管理员');
    }

    /**
     * 重置密码
     */
    public function resetPassword(string $password): self
    {
        $this->password = Hash::make($password);
        $this->save();
        
        return $this;
    }

    /**
     * 激活用户账号
     */
    public function activate(): self
    {
        if (!$this->isActive()) {
            $this->email_verified_at = Carbon::now();
            $this->save();
        }
        
        return $this;
    }

    /**
     * 停用用户账号
     */
    public function deactivate(): self
    {
        $this->email_verified_at = null;
        $this->save();
        
        return $this;
    }

    /**
     * 更新用户最后登录时间
     */
    public function updateLastLogin(): self
    {
        $this->last_login_at = Carbon::now();
        $this->save();
        
        return $this;
    }

    /**
     * 检查用户是否在线
     */
    public function isOnline(): bool
    {
        return $this->last_login_at !== null && 
               $this->last_login_at->diffInMinutes(Carbon::now()) < 15;
    }

    /**
     * 通过邮箱查找用户(静态方法)
     */
    public static function findByEmail(string $email)
    {
        return static::where('email', $email)->first();
    }

    /**
     * 通过用户名查找用户(静态方法)
     */
    public static function findByName(string $name)
    {
        return static::where('name', $name)->first();
    }

    /**
     * 通过用户名查找用户(作用域方法)
     */
    public function scopeWhereName(Builder $query, string $name): Builder
    {
        return $query->where('name', $name);
    }

    public static function getLevels(): array
    {
        return [
            1 => '普通会员',
            2 => '银卡会员',
            3 => '金卡会员',
            4 => '铂金卡会员',
            5 => '铂钻卡会员',
            6 => '黑钻卡会员',
        ];
    }
    
    // 创建用户时自动创建密码
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            // 如果密码为空则生成密码
            if (empty($user->password)) {
                $user->password = Hash::make(md5(time()));
            }
            // 如果邮箱为空则生成邮箱
            if (empty($user->email)) {
                $user->email = fake()->unique()->safeEmail();
                $user->email_verified_at = now();
            }
        });
    }

    public function getLevelNameAttribute()
    {
        return self::getLevels()[$this->level] ?? '普通会员';
    }
}
