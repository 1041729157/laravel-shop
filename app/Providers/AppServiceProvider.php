<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Yansongda\Pay\Pay; // 支付插件类

class AppServiceProvider extends ServiceProvider
{
    // 服务注册到容器
    public function register()
    {
        // 往服务容器中注入一个名为 alipay 的单例对象
        $this->app->singleton('alipay', function () {
            $config = config('pay.alipay');
            // 支付成功后前端回调地址，ngrok_url() 自定的辅助方法，helpers.php中
            $config['notify_url'] = ngrok_url('payment.alipay.notify');
            // 支付成功后服务器回调地址
            $config['return_url'] = route('payment.alipay.return');
            // 判断当前项目运行环境是否为线上环境
            // app()->environment() 获取当前运行的环境，线上环境会返回 production
            if (app()->environment() !== 'production') {
                // 对于支付宝，如果项目运行环境不是线上环境，则启用开发模式，并且将日志级别设置为 DEBUG
                $config['mode']         = 'dev';
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            // 调用 Yansongda\Pay 来创建一个支付宝支付对象
            return Pay::alipay($config);
        });

        $this->app->singleton('wechat_pay', function () {
            $config = config('pay.wechat');
            if (app()->environment() !== 'production') {
                // 由于微信支付没有开发模式，所以仅仅将日志级别设置为 DEBUG
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            // 调用 Yansongda\Pay 来创建一个微信支付对象
            return Pay::wechat($config);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
    }
}
