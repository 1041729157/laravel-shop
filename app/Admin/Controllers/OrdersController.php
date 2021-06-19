<?php 

namespace App\Admin\Controllers;

use App\Models\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;
use Encore\Admin\Layout\Content;
// 提供 validate 验证方法的基类（Laravel-Admin 的 Controller 基类并没有像 Laravel 默认的 Controller 基类那样提供了 validate 方法）
use Illuminate\Foundation\Validation\ValidatesRequests; 

class OrdersController extends AdminController
{
    use ValidatesRequests;

    protected $title = '订单';

    protected function grid()
    {
        $grid = new Grid(new Order);

        // 只展示已支付的订单，并且默认按支付时间倒序排序
        $grid->model()->whereNotNull('paid_at')->orderBy('paid_at', 'desc');

        $grid->no('订单流水号');
        // 展示关联关系的字段时，使用 column 方法
        $grid->column('user.name', '买家');
        $grid->total_amount('总金额')->sortable();
        $grid->paid_at('支付时间')->sortable();
        $grid->ship_status('物流')->display(function($value) {
            return Order::$shipStatusMap[$value];
        });
        $grid->refund_status('退款状态')->display(function($value) {
            return Order::$refundStatusMap[$value];
        });
        // 禁用创建按钮，后台不需要创建订单
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            // 禁用删除和编辑按钮
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        return $grid;
    }

    // 单信息比较多，Laravel-Admin的表单形式不能很好地满足需求，自定义后台的订单详情页面
    public function show($id, Content $content)
    {
        return $content
            ->header('查看订单')
            // body 方法可以接受 Laravel 的视图作为参数
            ->body(view('admin.orders.show', ['order' => Order::find($id)]));
    }

    public function ship(Order $order, Request $request)
    {
        // 判断当前订单是否已支付
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未付款');
        }
        // 判断当前订单发货状态是否为未发货
        if ($order->ship_status !== Order::SHIP_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已发货');
        }
        // Laravel 5.5 之后 validate 方法可以返回校验过的值
        $data = $this->validate($request, [
            'express_company' => ['required'],
            'express_no'      => ['required'],
        ], [], [
            // 汉化
            'express_company' => '物流公司',
            'express_no'      => '物流单号',
        ]);
        // 将订单发货状态改为已发货，并存入物流信息
        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            // 我们在 Order 模型的 $casts 属性里指明了 ship_data 是一个数组
            // 因此这里可以直接把数组传过去
            'ship_data'   => $data, 
        ]);

        // 返回上一页
        return redirect()->back();
    }
}
