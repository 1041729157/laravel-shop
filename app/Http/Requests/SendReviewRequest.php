<?php

namespace App\Http\Requests;

class SendReviewRequest extends FormRequest
{

    public function rules()
    {
        return [
            'reviews'          => ['required', 'array'],
            // // 检查 reviews 数组下每一个子数组的 id 参数
            'reviews.*.id'     => [
                'required',
                // $this->route('order')可以获得当前路由对应的订单对象，'order'对应的是路由中{order}参数。'当前路由'指的应该是控制器中绑定SendReviewRequest类的方法所对应的路由。
                Rule::exists('order_items', 'id')->where('order_id', $this->route('order')->id)
            ],
            'reviews.*.rating' => ['required', 'integer', 'between:1,5'],
            'reviews.*.review' => ['required'],
        ];
    }

    public function attributes()
    {
        return [
            'reviews.*.rating' => '评分',
            'reviews.*.review' => '评价',
        ];
    }
}
