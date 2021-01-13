@extends('layouts.app')
@section('title', '收货地址')

@section('content')
<div class="row">
  <div class="col-md-10 col-offset-md-1">
  	<div class="card panel-defalt">
  	  <div class="card-header">
  	    收货地址列表
  	    <a href="{{ route('user_addresses.create') }}" class="float-right">新增收货地址</a>
  	  </div>
  	  <div class="card-body">
  	  	<table class="table table-bordered table-striped">
  	  	  <thead>
  	  	  	<tr>
  	  	  	  <th>收货人</th>
  	  	  	  <th>地址</th>
  	  	  	  <th>邮编</th>
  	  	  	  <th>电话</th>
  	  	  	  <th>操作</th>
  	  	  	</tr>
  	  	  </thead>
  	  	  <tbody>
  	  	  	@foreach($addresses as $address)
  	  	  	<tr>
  	  	  	  <td>{{ $address->contact_name }}</td>
  	  	  	  <td>{{ $address->full_address }}</td>
  	  	  	  <td>{{ $address->zip }}</td>
  	  	  	  <td>{{ $address->contact_phone }}</td>
  	  	  	  <td>
  	  	  	  	<a href="{{ route('user_addresses.edit', ['user_address' => $address->id]) }}" class="btn btn-primary">修改</a>
                <!-- <form method="post" action="{{ route('user_addresses.destroy', ['user_address' => $address->id]) }}" style="display: inline-block;">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-danger" type="submit">删除</button>
                </form> -->
                <button class="btn btn-danger btn-del-address" type="button" data-id="{{ $address->id }}">删除</button>
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
<script type="text/javascript">
$(document).ready(function() {
  // 点击事件
  $('.btn-del-address').click(function() {
    // 获取 data-id 属性的值
    var id = $(this).data('id');
    // 调用 sweetalert JS库
    swal({
      title: "确认要删除该地址？",
      icon: "warning",
      buttons: ["取消", "确定"],
      dangerMode: true,
    })
    // 用户点击按钮后会触发这个回调函数
    // 用户点击确定 willDelete 的值为 true , 否则为 false
    .then(function(willDelete) {
      // 用户点击取消，直接返回
      if (!willDelete) {
        return;
      }
      // Ajax请求调用删除接口，用id来拼出请求的url
      // 由于laravel的Ajax请求已经封装好csrf_token，所以无需再手动添加
      axios.delete('/user_addresses/destroy/' + id)
        .then(function(data) {
          // 删除成功后提示
          swal({
            /*title: data.data.message, // 显示后台返回操作的结果
            text: "您已成功删除当前收货地址!",*/
            title: "删除成功！",
            icon: "success",
          })
          .then(function() {
            // 请求成功之后重新加载页面
            location.reload();
          });
        });
    });
  });
});
</script>
@stop