<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAddress;
use App\Http\Requests\UserAddressRequest;

class UserAddressesController extends Controller
{
    public function index(Request $request) {
    	// $request->user() 获取当前登录用户信息，user()并不是UserAddress中的方法
    	return view('user_addresses.index', ['addresses' => $request->user()->addresses]);
    }

    public function create() {
    	return view('user_addresses.create_and_edit', ['address' => new UserAddress()]);
    }

    public function store(UserAddressRequest $request) {
        $request->user()->addresses()->create($request->only([
            'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone',
        ]));

        return redirect()->route('user_addresses.index');
    }

    // 参数名 $user_address 必须和路由中的 {user_address} 一致才可以，否则页面无法获取数据
    public function edit(UserAddress $user_address) {
    	$this->authorize('own', $user_address);
    	return view('user_addresses.create_and_edit', ['address' => $user_address]);
    }

    public function update(UserAddress $user_address, UserAddressRequest $request) {
    	$this->authorize('own', $user_address);
    	$user_address->update($request->only([
    		'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone',
    	]));

    	return redirect()->route('user_addresses.index');
    }

    public function delete(UserAddress $user_address){
    	$this->authorize('own', $user_address);
    	$user_address->delete();
    	// return redirect()->route('user_addresses.index');
    	return [];
    }
}
