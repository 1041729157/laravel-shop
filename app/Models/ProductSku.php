<?php

namespace App\Models;

use App\Exceptions\InternalException;
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

    // 减少库存
    public function decreaseStock($amount)
    {
    	// $amount是库存需要减少的数量，不能小于零
        if ($amount < 0) {
            throw new InternalException('减库存不可小于0');
        }

        // 第二个where判断库存数量必须大于等于减少的数量
        return $this->where('id', $this->id)->where('stock', '>=', $amount)
        	// decrement()方法来减少字段的值，该方法会返回影响的行数，用来判断减库存操作是否成功
        	->decrement('stock', $amount);
    }

    // 增加库存
    public function addStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('加库存不可小于0');
        }
        // increment() 方法来保证操作的原子性
        $this->increment('stock', $amount);
    }
}
