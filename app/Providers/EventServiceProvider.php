<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // 事件和监听器关联起来
        \App\Events\OrderPaid::class => [
            \App\Listeners\UpdateProductSoldCount::class, // 显示交易数量
            \App\Listeners\SendOrderPaidMail::class, // 交易成功发送邮件通知
        ],
        \App\Events\OrderReviewed::class => [
            \App\Listeners\UpdateProductRating::class, // 修改商品评分和评价
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
