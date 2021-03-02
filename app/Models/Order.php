<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = "success";
    const REFUND_STATUS_FAILED = 'failed';

    const SHIP_STATUS_PENDING = 'pending';
    const SHIP_STATUS_DELIVERED = 'delivered';
    const SHIP_STATUS_RECEIVED = 'received';

    public static $refundStatusMap = [
    	self::REFUND_STATUS_PENDING    => '未退款',
    	self::REFUND_STATUS_APPLIED    => '已申请退款',
    	self::REFUND_STATUS_PROCESSING => '退款中',
    	self::REFUND_STATUS_SUCCESS    => '退款成功',
    	self::REFUND_STATUS_FAILED     => '退款失败',
    ];

    public static $shipStatusMap = [
    	self::SHIP_STATUS_PENDING   => '未发货',
    	self::SHIP_STATUS_DELIVERED => '已发货',
    	self::SHIP_STATUS_RECEIVED  => '已收货',
    ];

    protected $fillable = [
    	'no',
        'address',
        'total_amount', // 订单总金额
        'remark', // 订单备注
        'paid_at', // 支付时间
        'payment_method', // 支付方式
        'payment_no', // 支付平台订单号
        'refund_status', // 退款状态
        'refund_no', // 退款单号
        'closed', // 订单是否已关闭
        'reviewed', // 订单是否已评价
        'ship_status', // 物流状态
        'ship_data', // 物流数据
        'extra',
    ];

    protected $dates = [
    	'paid_at',
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 no 字段为空
            if (!$model->no) {
                // 调用 findAvailableNo 生成订单流水号
                $model->no = static::findAvailableNo();
                // 如果生成失败，则终止创建订单
                if (!$model->no) {
                    return false;
                }
            }
        });
    }

    public function user() {
    	return $this->belongsTo(User::class);
    }

    public function items() {
    	return $this->hasMany(OrderItem::class);
    }

    public static function findAvailableNo()
    {
        // 订单流水号前缀
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('no', $no)->exists()) {
                return $no;
            }
        }
        \Log::warning('find order no failed');

        return false;
    }
}
