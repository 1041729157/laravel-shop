@extends('layouts.app')
@section('title', $product->title)

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-body product-info">
    <div class="row">
      <div class="col-5">
        <img class="cover" src="{{ $product->image_url }}" alt="">
      </div>
      <div class="col-7">
        <div class="title">{{ $product->title }}</div>
        <div class="price"><label>价格</label><em>￥</em><span>{{ $product->price }}</span></div>
        <div class="sales_and_reviews">
          <div class="sold_count">累计销量 <span class="count">{{ $product->sold_count }}</span></div>
          <div class="review_count">累计评价 <span class="count">{{ $product->review_count }}</span></div>
          <div class="rating" title="评分 {{ $product->rating }}">评分 <span class="count">{{ str_repeat('★', floor($product->rating)) }}{{ str_repeat('☆', 5 - floor($product->rating)) }}</span></div>
        </div>
        <div class="skus">
          <label>选择</label>
          <!-- data-toggle 代表以什么事件触发 -->
          <!-- data-toggle="buttons" bootstrap标签选择器(active选择器) -->
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            @foreach($product->skus as $sku)
              <!-- data-toggle="tooltip" tooltip插件 是 bootstrap 的提示工具 -->
              <!-- title="{{ $sku->description }}" 提示内容 -->
              <!-- data-placement="bottom" 提示设置在下方 -->
              <!-- data-*="*" 将数据加入html标签，方便js捕获 -->
              <label class="btn sku-btn" data-price="{{ $sku->price }}" data-stock="{{ $sku->stock }}" data-toggle="tooltip" title="{{ $sku->description }}" data-placement="bottom">
              	<!-- type="radio" 单选框 -->
              	<!-- autocomplete="off" 禁止浏览器自动记录之前输入的值，默认为on -->
                <input type="radio" name="skus" autocomplete="off" value="{{ $sku->id }}"> {{ $sku->title }}
              </label>
            @endforeach
          </div>
        </div>
        <div class="cart_amount"><label>数量</label><input type="text" class="form-control form-control-sm" value="1"><span>件</span><span class="stock"></span></div>
        <div class="buttons">
          @if ($favored)
            <button class="btn btn-danger btn-disfavor">取消收藏</button>
          @else
            <button class="btn btn-success btn-favor">❤ 收藏</button>
          @endif
          <button class="btn btn-primary btn-add-to-cart">加入购物车</button>
        </div>
      </div>
    </div>
    <div class="product-detail">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" href="#product-detail-tab" aria-controls="product-detail-tab" role="tab" data-toggle="tab" aria-selected="true">商品详情</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#product-reviews-tab" aria-controls="product-reviews-tab" role="tab" data-toggle="tab" aria-selected="false">用户评价</a>
        </li>
      </ul>
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="product-detail-tab">
          {!! $product->description !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="product-reviews-tab">
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
@stop

@section('scriptAfterJs')
<script>
  $(document).ready(function () {
  	// tooltip 插件触发提示的方式，'hover'-鼠标经过时触发
  	$('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});
  	$('.sku-btn').click(function () {
  	  // 点击事件能触发对应的 .sku-btn 下的数据，应该是$(this)的功劳，指向当前选中的.sku-btn
  	  $('.product-info .price span').text($(this).data('price'));
  	  $('.product-info .stock').text('库存: ' + $(this).data('stock') + '件');
  	});

  	// 监听收藏按钮的点击事件
  	$('.btn-favor').click(function () {
  	  // 发起一个 post ajax 请求，请求 url 通过后端的 route() 函数生成。
  	  axios.post('{{ route('products.favor', ['product' => $product->id]) }}').then(function () { // 请求成功会执行这个回调
  	  	// swal() 弹出框
  	  	swal('操作成功', '', 'success')
  	  	  .then(function () {
  	  	    location.reload(); // 刷新页面
  	  	  });
  	  }, function (error) { // 请求失败会执行这个回调
  	  	// 如果返回码是 401 代表没登录
  	  	if (error.response && error.response.status === 401) {
  	  	  swal('请先登录', '', 'error');
  	  	} else if (error.response && (error.response.data.msg || error.response.data.message)) {
  	  	  // 其他有 msg 或者 message 字段的情况，将 msg 提示给用户
  	  	  swal(error.response.data.msg ? error.response.data.msg : error.response.data.message, '', 'error');
  	  	} else {
  	  	  // 其他情况应该是系统挂了
  	  	  swal('系统错误', '', 'error');
  	  	}
  	  });
  	});

  	// 取消收藏
  	$('.btn-disfavor').click(function () {
  	  axios.delete('{{ route('products.disfavor', ['product' => $product->id]) }}')
  	    .then(function () {
  	      swal('操作成功', '', 'success')
  	        .then(function () {
  	          location.reload();
  	        });
  	    });
  	});

    // 加入购物车
    $('.btn-add-to-cart').click(function() {
      axios.post('{{ route('cart.add') }}', {
        // val()返回对应的value属性，val(a)括号内有内容则为设置value属性
        // label.active 触发选择器后出现名为active的类(页面可以看到)
        sku_id: $('label.active input[name=skus]').val(),
        amount: $('.cart_amount input').val(),
      })
        .then(function () {
          swal('加入购物车成功', '', 'success');
        }, function (error) {
          if (error.response.status === 401) {
            swal('请先登录', '', 'error');
          } else if (error.response.status === 422) {
            var html = '<div>'
            // _.each() lodash的遍历方法
            // _.each(collection, iteratee) iteratee 调用3个参数： (value, index|key, collection)
            // 第一层遍历获取到所以错误的值的集合
            _.each(error.response.data.errors, function (errors) {
              // 第二层遍历不知道干什么用的，去除了输出结果也一样
              _.each(errors, function (error) {
                html += error + '<br>';
              })
            });
            html += '<div>';
            swal({content: $(html)[0], icon: 'error'})
          } else {
            swal('系统错误', '', 'error');
          }
        })
    });
  });
</script>
@stop