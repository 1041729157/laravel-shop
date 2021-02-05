<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;

class CartController extends Controller
{
    public function add(AddCartRequest $request) {
    	$user = $request->user();
    	$skuId = $request->input('sku_id');
    	$amount = $request->input('amount');

    	// 利用用户和购物车模型的关联关系查询该商品是否已在购物车中
	    if ($cart = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
	    	$cart->update([
	    		'amount' => $cart->amount + $amount,
	    	]);
	    } else {
	    	// 否则创建一个新的购物车记录
	    	$cart = new CartItem(['amount' => $amount]);
	    	$cart->user()->associate($user); // 将用户id添加到user_id字段
	    	$cart->productSku()->associate($skuId); // 将sku_id添加到product_sku_id
	    	$cart->save();
	    }

	    return [];
    }
}
