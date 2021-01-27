<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InvalidRequestException extends Exception
{
    public function __construct(string $message = "", int $code = 400) {
    	// 将错误信息打印给开发者查看
    	parent::__construct($message, $code);
    }

    // 当此异常类被触发时系统会调用 render() 方法来输出
    public function render(Request $request) {
    	// 如果是AJAX请求则返回JSON格式的数据
    	if ($request->expectsJson()) {
    		// json() 方法第二个参数就是 Http 返回码
    		return response()->json(['msg' => $this->message], $this->code);
    	}

    	return view('pages.error', ['msg' => $this->message]);
    }
}
