<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'), 
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    // index、create、store、edit、update这些方法都在他们控制器继承的AdminController中，里面会调用对应的控制器方法。（路径vendor\encore\laravel-admin\src\Controllers）
    $router->get('/', 'HomeController@index')->name('admin.home');

    $router->get('users', 'UsersController@index');

    $router->get('products', 'ProductsController@index');

    $router->get('products/create', 'ProductsController@create');

    $router->post('products', 'ProductsController@store');

    $router->get('products/{id}/edit', 'ProductsController@edit');

    $router->put('products/{id}', 'ProductsController@update');

    // 后台订单列表
    $router->get('orders', 'OrdersController@index')->name('admin.orders.index');

    // 后台订单详情
    $router->get('orders/{order}', 'OrdersController@show')->name('admin.orders.show');

    // 发货
    $router->post('orders/{order}/ship', 'OrdersController@ship')->name('admin.orders.ship');
});
