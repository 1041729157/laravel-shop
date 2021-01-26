<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller
{
    public function index(Request $request) {
    	/*
    	// 查找 on_sale 值为true的商品
    	$products = Product::query()->where('on_sale', true)->paginate(16);
    	return view('products.index', ['products' => $products]);
    	*/

    	// 创建一个查询构造器，如果使用'Product'的实例进行查询，获取到的只是数据，和查询构造器完全不一样
    	$builder = Product::query()->where('on_sale', true);

    	// 判断是否有提交 search 参数，如果有就赋值给 $seach 变量
    	// search 参数用来模糊搜索商品
    	if ($search = $request->input('search', '')) {
   			// % 能匹配多字符 (模糊搜索)
			// _ 只能匹配一种字符 (精确搜索)
    		$like = '%'.$search.'%';
    		// 模糊搜索商品标签、商品详情、SKU 标题、SKU 描述
    		$builder->where(function ($query) use ($like) {
    			// like 搜索(一种 SQL 查询形式)
    			$query->where('title', 'like', $like)
    			      ->orWhere('description', 'like', $like)
    			      // 'skus' 商品跟 SKU 的关联关系
    			      ->orWhereHas('skus', function ($query) use ($like) {
    			      	$query->where('title', 'like', $like)
    			      		  ->orWhere('description', 'like', $like);
    			      });
    		});
    	}

    	// 是否有提交 order 参数，如果有就赋值给 $order 变量
        // order 参数用来控制商品的排序规则
        if ($order = $request->input('order', '')) {
            // 是否是以 _asc 或者 _desc 结尾
            // $m[0]将包含完整模式匹配到的文本(price_asc等)，$m[1]将包含第一个捕获子组匹配到的文本(price等)，$m[2]将包含第二个捕获子组匹配到的文本(asc等)，以此类推
            // 正则表达式中一个 () 代表一个子组
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                // 如果字符串的开头是这 3 个字符串之一，说明是一个合法的排序值
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    // 根据传入的排序值来构造排序参数
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }

        // 分页
    	$products = $builder->paginate(16);

    	return view('products.index', [
    		'products' => $products,
    		// 用户的搜索和排序内容
    		'filters' => [
    			'search' => $search,
    			'order' => $order,
    		],
    	]);
    }
}