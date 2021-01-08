<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAddress;

class UserAddressesController extends Controller
{
    public function index(Request $request) {
    	return view('user_addresses.index', ['addresses' => $request->user()->addresses]);
    }
}
