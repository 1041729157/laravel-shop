<?php 

function route_class() {
  return str_replace('.', '-', Route::currentRouteName());
}

function get_db_config()
{
    if (getenv('IS_IN_HEROKU')) {
        $url = parse_url(getenv("DATABASE_URL"));

        return $db_config = [
            'connection' => 'pgsql',
            'host' => $url["host"],
            'database'  => substr($url["path"], 1),
            'username'  => $url["user"],
            'password'  => $url["pass"],
        ];
    } else {
        return $db_config = [
            'connection' => env('DB_CONNECTION', 'mysql'),
            'host' => env('DB_HOST', 'localhost'),
            'database'  => env('DB_DATABASE', 'forge'),
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
        ];
    }
}

// 为了方便调用反向代理的域名+详细页面地址（如http://92c8ef752cdb.ngrok.io/products），第一个参数接收路由名，如'products.index'。第二个参数不知道
function ngrok_url($routeName, $parameters = [])
{
    // 开发环境，并且配置了 NGROK_URL (内网穿刺，反向代理地址)
    if(app()->environment('local') && $url = config('app.ngrok_url')) {
        // route() 函数第三个参数代表是否绝对路径
        return $url.route($routeName, $parameters, false);
    }

    return route($routeName, $parameters);
}