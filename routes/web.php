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
Route::get('products/{product}', 'ProductsController@show')->name('products.show');

// 用户认证脚手架路由
Auth::routes(['verify' => true]); //'verify' => true 启用邮箱验证相关的路由

// auth 中间件代表需要登录，verified中间件代表需要经过邮箱验证
Route::group(['middleware' => ['auth', 'verified']], function () {
	Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
	Route::get('user_addresses/create', 'UserAddressesController@create')->name('user_addresses.create');
	Route::post('user_addresses/store', 'UserAddressesController@store')->name('user_addresses.store');
	Route::get('user_addresses/edit/{user_address}', 'UserAddressesController@edit')->name('user_addresses.edit');
	Route::put('user_addresses/update/{user_address}', 'UserAddressesController@update')->name('user_addresses.update');
	Route::delete('user_addresses/destroy/{user_address}', 'UserAddressesController@delete')->name('user_addresses.destroy');
	// 收藏商品
	Route::post('products/{product}/favorite', 'ProductsController@favor')->name('products.favor');
	// 取消收藏
	Route::delete('products/{product}/favorite', 'ProductsController@disfavor')->name('products.disfavor');
});
