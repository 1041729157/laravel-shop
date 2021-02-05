<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['amount'];
    public $timestamps = false;

    // 购物车内的商品只属于一个用户
    public function user() {
    	return $this->belongsTo(User::class);
    }

    // 购物车内的商品对应一个SKU
    public function productSku() {
    	return $this->belongsTo(ProductSku::class);
    }
}
