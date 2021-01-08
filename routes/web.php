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
Route::get('/', 'PagesController@root')->name('root'); //->middleware('verified');

// 用户认证脚手架路由
Auth::routes(['verify' => true]); //'verify' => true 启用邮箱验证相关的路由

// auth 中间件代表需要登录，verified中间件代表需要经过邮箱验证
Route::group(['middleware' => ['auth', 'verified']], function () {
	Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
});
