<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductsController extends AdminController
{
    protected $title = '商品';

    // 后台列表要展示的内容
    protected function grid()
    {
        $grid = new Grid(new Product());

        $grid->id('ID')->sortable(); //sortable()将这一列设置为可排序列
        $grid->title('商品名称');
        $grid->on_sale('已上架')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->price('价格');
        $grid->rating('评分');
        $grid->sold_count('销量');
        $grid->review_count('评论数');

        $grid->actions(function ($actions) {
            $actions->disableView(); //
            $actions->disableDelete(); // 删除按钮
        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Product::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('description', __('Description'));
        $show->field('image', __('Image'));
        $show->field('on_sale', __('On sale'));
        $show->field('rating', __('Rating'));
        $show->field('sold_count', __('Sold count'));
        $show->field('review_count', __('Review count'));
        $show->field('price', __('Price'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    // 创建和编辑商品
    protected function form()
    {
        $form = new Form(new Product());

        // // 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
        $form->text('title', '商品名称')->rules('required');

        // 创建一个图片选择框
        $form->image('image', '封面图片')->rules('required|image');

        // 创建一个富文本编辑器，quill()是第三方扩展包的方法，具体配置在config/admin.php中的 extensions 段
        $form->quill('description', '商品描述')->rules('required');

        // $form->radio('on_sale', '上架') 在表单中创建一组单选框，options(['1' => '是', '0'=> '否']) 设置两个选项，default('0') 代表默认选择值为 0 的框，在我们这里就是默认为 否
        $form->radio('on_sale', '上架')->options(['1' => '是', '0' => '否'])->default(0);

        // $form->hasMany('skus', 'SKU 列表', /**/) 可以在表单中直接添加一对多的关联模型，商品和商品 SKU 的关系就是一对多，第一个参数必须和主模型中定义此关联关系的方法同名，我们之前在 App\Models\Product 类中定义了 skus() 方法来关联 SKU，因此这里我们需要填入 skus，第二个参数是对这个关联关系的描述，第三个参数是一个匿名函数，用来定义关联模型的字段
        $form->hasMany('skus', 'SKU 列表', function (Form\NestedForm $form) {
            $form->text('title', 'SKU 名称')->rules('required');
            $form->text('description', 'SKU 描述')->rules('required');
            $form->text('price', '单价')->rules('required|numeric|min:0.01');
            $form->text('stock', '剩余库存')->rules('required|integer|min:0');
        });

        // 定义时间回调，当模型即将保存时会触发这个回调
        // $form->saving() 用来定义一个事件回调，当模型即将保存时会触发这个回调。我们需要在保存商品之前拿到所有 SKU 中最低的价格作为商品的价格，然后通过 $form->model()->price 存入到商品模型中
        $form->saving(function (Form $form) {
            // collect() 函数是 Laravel 提供的一个辅助函数，可以快速创建一个 Collection 对象。在这里我们把用户提交上来的 SKU 数据放到 Collection 中，利用 Collection 提供的 min() 方法求出所有 SKU 中最小的 price，后面的 ?: 0 则是保证当 SKU 数据为空时 price 字段被赋值 0
            // where(Form::REMOVE_FLAG_NAME, 0) 只查询未被移除(删除)的 SKU ，也就是 SKU 中 _remove_ 字段为 0 的SKU商品 (当在前端移除一个 SKU 的之后，点击保存按钮时 Laravel-Admin 仍然会将被删除的 SKU 提交上去，但是会添加一个 _remove_=1 的字段)
            $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
        });

        return $form;
    }
}
