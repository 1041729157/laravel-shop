<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductSku;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
    	'title', 'description', 'image', 'on_sale',
    	'rating', 'sold_count', 'review_count', 'price'
    ];

    // 数据类型转换
    protected $casts = [
    	// 转换为布尔值，当访问 on_sale 属性时，将获取到布尔值(true或false)，即使其在数据存储的是整型(0和1)
    	'on_sale' => 'boolean',
    ];

    public function skus() {
    	return $this->hasMany(ProductSku::class);
    }

    // 访问器
    public function getImageUrlAttribute() {
    	// 如果 image 字段本身就已经是完整的 url 就直接返回
    	// Str::starsWith(a, b)，判断参数 a 是否以 b 作为开头
    	// $this->attributes['image'] 和 $this->image没有区别
    	if (Str::startsWith($this->attributes['image'], ['http://', 'https://'])) {
    		return $this->attributes['image'];
    	}
    	// 返回完整的图片链接
    	// \Storage::disk('public') 的参数 public 需要和 config/admin.php 里面的 upload.disk 配置一致
    	return \Storage::disk('public')->url($this->attributes['image']);
    }
}
