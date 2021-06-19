<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use App\Models\ProductSku;
use App\Services\CartService; // 将部分逻辑封装到这个类中

class CartController extends Controller
{

    protected $cartService;

    // 直接注入，不需要管耦合（本来就是重构代码）
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    // 购物车商品页面
    public function index(Request $request) {
    	// 'productSku.product'相当于$cartItem->productSku->product，预加载购物车内的商品对应的SKU所对应的商品
    	$cartItems = $request->user()->cartItems()->with(['productSku.product'])->get();
        // 获取收货地址
        $addresses = $request->user()->addresses()->orderBy('last_used_at', 'desc')->get();
    	return view('cart.index', ['cartItems' => $cartItems, 'addresses' => $addresses]);
    }

    // 
    public function add(AddCartRequest $request) 
    {
        // $request->input('sku_id')获取页面提交名为'sku_id'的值，并非是名为'sku_id'的input的值，$request->input()获取数据的意思
        $this->cartService->add($request->input('sku_id'), $request->input('amount'));

        return [];
    }

    // 移除商品
    public function remove(ProductSku $sku, Request $request)
    {
    	$this->cartService->remove($sku->id);

        return [];
    }
}
