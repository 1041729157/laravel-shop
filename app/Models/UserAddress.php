<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserAddress extends Model
{
    protected $fillable = [
    	'province',
    	'city',
    	'district',
    	'address',
    	'zip',
    	'contact_name',
    	'contact_phone',
    	'last_used_at',
    ];

    // 创建Carbon对象，Carbon 是 Laravel 默认使用的时间日期处理类
    protected $dates = ['last_used_at'];

    public function user() {
    	return $this->belongsTo(User::class);
    }

    // 访问器，驼峰式命名->get访问器名称Attribute，调用访问器：$this->full_address
    // 获取完整地址
    public function getFullAddressAttribute() {
    	return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }
}
