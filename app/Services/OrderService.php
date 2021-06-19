<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\ProductSku;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloserOrder; // 自定义的队列任务类
use Carbon\Carbon;

// $request 不可以出现在控制器和中间件以外的地方，根据职责单一原则，获取数据这个任务应该由控制器来完成，封装的类只需要专注于业务逻辑的实现
class OrderService
{
    public function store(User $user, UserAddress $address, $remark, $items)
    {
        // 开启一个数据库事务
        $order = \DB::transaction(function () use ($user, $address, $remark, $items) {
            // 更新此地址的最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            // 创建一个订单
            $order   = new Order([
                'address'      => [ // 将地址信息放入订单中
                    'address'       => $address->full_address,
                    'zip'           => $address->zip,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark'       => $remark,
                'total_amount' => 0,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();

            $totalAmount = 0;
            // 遍历用户提交的 SKU
            foreach ($items as $data) {
                $sku  = ProductSku::find($data['sku_id']);
                // 创建一个 OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price'  => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $totalAmount += $sku->price * $data['amount'];
                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throw new InvalidRequestException('该商品库存不足');
                }
            }
            // 更新订单总金额
            $order->update(['total_amount' => $totalAmount]);

            // 将下单的商品从购物车中移除
            // 将下单的商品从购物车中移除。collect() 辅助函数快速取得所有 SKU ID
            $skuIds = collect($items)->pluck('sku_id')->all();
            // 同文件夹下的CartService
            // 通过laravel容器函数app()创建CartService实例，可以自动解析 CartService 需要的依赖（后面可能会在这个类中注入依赖）
            // https://learnku.com/courses/laravel-shop/7.x/encapsulation-business-code/7849
            app(CartService::class)->remove($skuIds);

            return $order;
        });

        // 调用关闭订单的队列任务，如果订单在一定时间内未支付，自动关闭
        // 这里我们直接使用 dispatch 辅助函数，在控制器中$this->dispatch()的写法
        dispatch(new CloserOrder($order, config('app.order_ttl')));

        return $order;
    }
}