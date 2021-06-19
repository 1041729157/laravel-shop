<?php

namespace App\Services;

use Auth;
use App\Models\CartItem;

class CartService
{
    public function get()
    {
        return Auth::user()->cartItems()->with(['productSku.product'])->get();
    }

    public function add($skuId, $amount)
    {
        $user = Auth::user();
        // 从数据库中查询该商品是否已经在购物车中
        // 利用用户和购物车模型的关联关系查询该商品是否已在购物车中
        if ($item = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
            $item->update([
                'amount' => $item->amount + $amount,
            ]);
        } else {
            // 否则创建一个新的购物车记录
            $item = new CartItem(['amount' => $amount]);
            $item->user()->associate($user); // 将用户id添加到user_id字段
            $item->productSku()->associate($skuId); // 将sku_id添加到product_sku_id
            $item->save();
        }

        return $item;
    }

    public function remove($skuIds)
    {
        // 可以传单个 ID，也可以传 ID 数组
        if (!is_array($skuIds)) {
            $skuIds = [$skuIds];
        }
        // 登录用户购物车的object对象以前端传入路由的sku的id为基准，查询购物车数据库中对应的product_sku_id的行，调用delete()方法进行删除
        Auth::user()->cartItems()->whereIn('product_sku_id', $skuIds)->delete();
    }
}