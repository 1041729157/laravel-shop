<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use App\Models\ProductSku;

class CartController extends Controller
{
    public function add(AddCartRequest $request) {
    	$user = $request->user();
    	// $request->input('sku_id')获取页面提交名为'sku_id'的值，并非是名为'sku_id'的input的值，$request->input()获取数据的意思
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

    // 购物车商品页面
    public function index(Request $request) {
    	// 'productSku.product'相当于$cartItem->productSku->product，预加载购物车内的商品对应的SKU所对应的商品
    	$cartItem = $request->user()->cartItems()->with(['productSku.product'])->get();
    	return view('cart.index', ['cartItem' => $cartItem]);
    }

    // 移除商品
    public function remove(ProductSku $sku, Request $request) {
    	// 登录用户购物车的object对象以前端传入路由的sku的id为基准，查询购物车数据库中对应的product_sku_id的行，调用delete()方法进行删除
    	$request->user()->cartItems()->where('product_sku_id', $sku->id)->delete();
    	return [];
    }
}
