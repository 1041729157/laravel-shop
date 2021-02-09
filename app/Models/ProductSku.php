<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class ProductSku extends Model
{
    protected $fillable = [
    	'title', 'description', 'price', 'stock'
    ];

    // 一个SKU属于一个商品
    public function product() {
    	return $this->belongsTo(Product::class);
    }
}
