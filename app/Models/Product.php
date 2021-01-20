<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductSku;

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
}
