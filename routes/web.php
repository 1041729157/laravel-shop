<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/
// Route::get('/', 'PagesController@root')->name('root'); //->middleware('verified');

Route::redirect('/', '/products')->name('root');
Route::get('products', 'ProductsController@index')->name('products.index');
// 收藏列表页（由于会跟下面的products/{product}路由冲突，所以要放在它上面）
Route::get('products/favorites', 'ProductsController@favorites')->name('products.favorites');
Route::get('products/{product}', 'ProductsController@show')->name('products.show');

// 用户认证脚手架路由
Auth::routes(['verify' => true]); //'verify' => true 启用邮箱验证相关的路由

// auth 中间件代表需要登录，verified中间件代表需要经过邮箱验证
Route::group(['middleware' => ['auth', 'verified']], function () {
	// 收货地址页
	Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
	// 新建收货地址页
	Route::get('user_addresses/create', 'UserAddressesController@create')->name('user_addresses.create');
	// 创建收货地址
	Route::post('user_addresses/store', 'UserAddressesController@store')->name('user_addresses.store');
	// 修改收货地址页
	Route::get('user_addresses/edit/{user_address}', 'UserAddressesController@edit')->name('user_addresses.edit');
	// 修改收货地址
	Route::put('user_addresses/update/{user_address}', 'UserAddressesController@update')->name('user_addresses.update');
	// 删除收货地址
	Route::delete('user_addresses/destroy/{user_address}', 'UserAddressesController@delete')->name('user_addresses.destroy');
	// 收藏商品
	Route::post('products/{product}/favorite', 'ProductsController@favor')->name('products.favor');
	// 取消收藏
	Route::delete('products/{product}/favorite', 'ProductsController@disfavor')->name('products.disfavor');
	// 添加购物车
	Route::post('cart', 'CartController@add')->name('cart.add');
	// 购物车详情
	Route::get('cart', 'CartController@index')->name('cart.index');
	// 删除购物车商品
	Route::delete('cart/{sku}', 'CartController@remove')->name('cart.remove');
	// 创建订单
	Route::post('orders', 'OrdersController@store')->name('orders.store');
	// 订单总页面
	Route::get('orders', 'OrdersController@index')->name('orders.index');
	// 订单详情页面
	Route::get('orders/{order}', 'OrdersController@show')->name('orders.show');
	// 订单支付宝支付页面
	Route::get('payment/{order}/alipay', 'PaymentController@payByAlipay')->name('payment.alipay');
	// 支付完成后的前端回调（页面跳转）
	Route::get('payment/alipay/return', 'PaymentController@alipayReturn')->name('payment.alipay.return');
	// 确认收货
	Route::post('orders/{order}/received', 'OrdersController@received')->name('orders.received');
	// 发布评价页面
	Route::get('orders/{order}/review', 'OrdersController@review')->name('orders.review.show');
	// 发布评价
    Route::post('orders/{order}/review', 'OrdersController@sendReview')->name('orders.review.store');
});

// 支付成功后的服务器回调（服务器端回调的路由不能放到带有auth中间件的路由组中，因为支付宝的服务器请求不会带有认证信息）
Route::post('payment/alipay/notify', 'PaymentController@alipayNotify')->name('payment.alipay.notify');


