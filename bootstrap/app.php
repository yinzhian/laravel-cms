<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

// http://laravelcms.demo/api/index
// http://dev-laravelcms.demo/api/index

/// TODO 根据域名、判断不同环境加载不同的 env

// 默认加载的文件 - 正式
$env = ".env";
$app_env = "";

if (!$app->runningInConsole()) {
    //HTTP形式
    if (empty($_SERVER['HTTP_HOST'])) {
        die('[error] no host');
    }
    $app_env = substr($_SERVER['HTTP_HOST'],0,strpos($_SERVER['HTTP_HOST'],'-'));
} else {
    //其它形式
    //$app_env = $app->detectEnvironment(function () {
    //    return 'prd';
    //});
}

// 拼接文件名
$env = $app_env ? $env.".".$app_env : $env;

//if (empty($app_env)) {
//    die('[error] no environment');
//}

//写入环境配置
//Dotenv::setEnvironmentVariable('APP_ENV', $app_env);
$app->loadEnvironmentFrom($env);
//$app->loadEnvironmentFrom('.env.' . $app_env);
//    ->useEnvironmentPath(base_path('env'));


return $app;
