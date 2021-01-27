<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InternalException extends Exception
{
    protected $msgForUser;

    // 第一个参数就是原本应该有的异常信息比如连接数据库失败,第二个参数是展示给用户的信息
    public function __construct(string $message, string $msForUser = '系统内部错误', int $code = 500) {
    	// 将错误信息打印给开发者查看
    	parent::__construct($message, $code);
    	$this->msgForUser = $msgForUser;
    }

    // 当此异常类被触发时系统会调用 render() 方法来输出
    public function render(Request $request) {
    	// 如果是AJAX请求则返回JSON格式的数据
    	if ($request->expectsJson()) {
    		// json() 方法第二个参数就是 Http 返回码
    		return response()->json(['msg' => $this->msgForUser], $this->code);
    	}

    	return view('pages.error', ['msg' => $this->msgForUser]);
    }
}
