<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserAddress;
use Encore\Admin\Traits\DefaultDatetimeFormat; // 修改后台注册时间显示格式

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, DefaultDatetimeFormat;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function addresses() {
        return $this->hasMany(UserAddress::class);
    }
}
