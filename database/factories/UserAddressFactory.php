<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\UserAddress;
use Faker\Generator as Faker;

$factory->define(UserAddress::class, function (Faker $faker) {

    $addresses = [
    	["北京市", "市辖区", "东城区"],
        ["河北省", "石家庄市", "长安区"],
        ["江苏省", "南京市", "浦口区"],
        ["江苏省", "苏州市", "相城区"],
        ["广东省", "深圳市", "福田区"],
    ];

    $address = $faker->randomElement($addresses);

    return [
    	'province'      => $address[0],
    	'city'          => $address[1],
    	'district'      => $address[2],
    	//sprintf()把百分号（%）符号替换成一个作为参数进行传递的变量; %d - 包含正负号的十进制数（负数、0、正数）
    	'address'       => sprintf('第%d街道第%d号', $faker->randomNumber(2), $faker->randomNumber(3)),
    	'zip'           => $faker->postcode,
    	'contact_name'  => $faker->name,
    	'contact_phone' => $faker->phoneNumber,
    ];
});
