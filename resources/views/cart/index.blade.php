@extends('layouts.app')
@section('title', '购物车')

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="cart">
  <div class="cart-heard">我的购物车</div>
  <div class="cart-body">
  	<table class="table table-striped">
  	  <thead>
  	  	<tr>
  	  	  <!-- 当checkbox勾选框被选中之后，当前标签内会增加一个checked(checked='checked')属性，代表已勾选此选项框，但页面不可见，手动添加的话便是可见 -->
  	  	  <th><input type="checkbox" id="select-all" name=""></th>
  	  	  <th>商品信息</th>
  	  	  <th>单价</th>
  	  	  <th>数量</th>
  	  	  <th>操作</th>
  	  	</tr>
  	  </thead>
  	  <tbody class="product_list">
  	  	@foreach($cartItem as $item)
  	  	<tr data-id="{{ $item->productSku->id }}">
  	  	  <td>
  	  	  	<input type="checkbox" name="select" value="{{ $item->productSku->id }}" {{ $item->productSku->product->on_sale ? 'checked' : 'disabled' }}>
  	  	  	<td class="product_info">
  	  	  	  <div class="preview">
  	  	  	  	<!-- 
  	  	  	  	  _blank – 在新窗口中打开链接 
  	  	  	  	  _parent – 在父窗体中打开链接 
  	  	  	  	  _self – 在当前窗体打开链接,此为默认值 
  	  	  	  	  _top – 在当前窗体打开链接,并替换当前的整个窗体 
  	  	  	  	  -->
  	  	  	  	<a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">
  	  	  	  	  <img src="{{ $item->productSku->product->image_url }}">
  	  	  	  	</a>
  	  	  	  </div>
  	  	  	  <div @if(!$item->productSku->product->on_sale) class="not_on_sale" @endif>
  	  	  	  	<span class="product_title">
  	  	  	  	  <a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">{{ $item->productSku->product->title }}</a>
  	  	  	  	</span>
  	  	  	  	<span class="sku_title">
  	  	  	  	  {{ $item->productSku->title }}
  	  	  	  	</span>
  	  	  	  	@if(!$item->productSku->product->on_sale)
  	  	  	  	  <span class="warning">该商品已下架</span>
  	  	  	  	@endif
  	  	  	  </div>
  	  	  	</td>
  	  	  	<td><span class="price">￥{{ $item->productSku->price }}</span></td>
  	  	  	<td>
	          <input type="text" class="form-control form-control-sm amount" @if(!$item->productSku->product->on_sale) disabled @endif name="amount" value="{{ $item->amount }}">
	         </td>
  	  	  	<td>
  	  	  	  <button class="btn btn-sm btn-danger btn-remove">移除</button>
  	  	  	</td>
  	  	  </td>
  	  	</tr>
  	  	@endforeach
  	  </tbody>
  	</table>
  </div>
</div>
</div>
</div>
@stop

@section('scriptAfterJs')
<script>
  $(document).ready(function () {
  	// $(this) 可以获取到当前点击的 移除 按钮的 jQuery 对象
    // closest() 方法可以获取到匹配选择器的第一个祖先元素，在这里就是当前点击的'移除' 按钮之上的 <tr> 标签
    // data('id') 方法可以获取到我们之前设置的 data-id 属性的值，也就是对应的 SKU id
  	$('.btn-remove').click(function () {
  	  var id = $(this).closest('tr').data('id');
  	  swal({
  	  	title: "确认要将该产品移除？",
  	  	icon: "warning",
  	  	buttons: ['取消', '确定'],
  	  	dangerMode: true, // 设置 dangerMode 为 true ，焦点将在“取消”按钮而不是“确认”按钮上，并且“确认”按钮将为红色以强调危险操作
  	  })
  	  .then(function (willDelete) {
  	  	// 用户点击 确定 按钮，willDelete 的值就会是 true，否则为 false
  	  	if (!willDelete) {
  	  	  return;
  	  	}
  	  	axios.delete('/cart/' + id)
  	  	  .then(function () {
  	  	  	location.reload();
  	  	  })
  	  });
  	});

  	// 监听 全选/取消全选 单选框的变更事件
  	$('#select-all').change(function() {
      // 获取#select-all单选框的选中状态，选中则获取到checked的属性，未选中则获取到空
      // prop()方法可以知道标签中是否包含某个属性，当单选框被勾选时，对应的标签就会新增一个 checked 的属性
      var checked = $(this).prop('checked');
      // 获取所有 name=select 并且不带有 disabled 属性的勾选框
      // 对于已经下架的商品我们不希望对应的勾选框会被选中，因此我们需要加上 :not([disabled]) 这个条件
      // each()是jQuery的遍历方法，写法:$(selector).each(function(index,element))，具体用法看百度
      $('input[name=select][type=checkbox]:not([disabled])').each(function() {
        // 将其勾选状态设为与目标单选框一致
        $(this).prop('checked', checked);
      });
  	});
  });
</script>
@stop