### 环境
    PHP V8.0
    laravel V8.83
    composer V2.1.14
---
### 安装
    拉取代码对应的分支
    完善 storate 目录
    执行 composer install
    创建 .env 文件, 并修改配置信息
    修改底层数据迁移时自动处理 created_at和updated_at 
        打开 database/migrations/ 下随便一个文件、ctrl 单机 timestamps() 修改如下
        （文件：vendor/laravel/framework/src/Illuminate/Database/Schema/Blueprint.php  行号：1134）
            public function timestamps($precision = 0)
            {
                $this->timestamp('created_at', $precision)->useCurrent();
                $this->timestamp('updated_at', $precision)->nullable()->useCurrentOnUpdate();
            }
    迁移数据执行 php artisan migrate
    填充后台账号信息 php artisan db:seed --class=AdminSeed
    启动项目 php artisan serve --port=8080
    登录地址 127.0.0.7:8080/admin/login
    账号 admin
    密码 admin123
     
    接口文档地址：https://console-docs.apipost.cn/cover.html?url=f6be9c5b8d3dfc77&salt=81d440388d36791e
    获取密码请联系：yza8023@qq.com 
---
### 开发环境
    添加文件
    .env      正式环境配置文件 对应域名 不加前缀
    .env.dev  测试环境配置文件 对应域名 dev-
    修改文件
        bootstrap/app.php

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
                $app_env = $app->detectEnvironment(function () {
                    return 'prd';
                });
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
---
### 修改
    1. .env
        APP_PAGE=20  全局分页数量
        TIME_ZONE=PRC  时区
        LOG_CHANNEL=daily  每天一个日志文件
        DB_PREFIX=cms_  数据库前缀
        APP_SUPER=SuperAdmin 超级管理员角色
    
    2. 添加路由文件
        app/Providers/RouteServiceProvider.php
            Route::prefix('admin')
                ->middleware('admin')
                ->name('admin.')
                ->namespace($this->namespace)
                ->group(base_path('routes/admin.php'));
        routers 目录下创建 admin.php
    
    3. 全局异常捕捉
        app/Exceptions/Handler.php
            public function render( $request, Throwable $exception )
            {
                if ( $request->is( 'api/*' ) || $request->is( 'admin/*' ) ) {
        
                    // 获取错误信息
                    $error = $this->convertExceptionToResponse( $exception );
        
                    Log::error( "***** 统一捕捉错误 START ************************" );
                    Log::error(
                        "统一捕捉错误 === ",
                        [
                            "http_code"       => $error->getStatusCode(),
                            "diy_status_code" => $exception->getCode(),
                            "message"         => $exception->getMessage(),
                            "file"            => $exception->getFile(),
                            "line"            => $exception->getLine()
                        ]
                    );
                    Log::error( "***** 统一捕捉错误 END **************************" );
        
                    return fail( (int) $exception->getCode(), (string) $exception->getMessage(), (int) $error->getStatusCode() );
        
                } else {
                    return parent::render( $request, $exception );
                }
            }
---
### 自定义创建 Service 命令
    创建命令：php artisan make:command makeService
    创建模板：app/Console/Templates/services.stub
    设置路径：app\Http\Services\
    使用方式：php artisan make:service DemoService
---
### 监听 SQL
    创建监听文件 app/Listeners/QueryListener.php
        use Monolog\Handler\RotatingFileHandler;
        use Monolog\Logger;

        public function handle(QueryExecuted $event)
        {
            if (env('APP_ENV', 'production') == 'local') {
                $sql = str_replace("?", "'%s'", $event->sql);
                $log = vsprintf($sql, $event->bindings);

                // 自定义SQL位置
                ( new Logger( env( "APP_ENV", "local" ) ) )
                    ->pushHandler( new RotatingFileHandler( storage_path( "logs/sql/sql.log" ) ) )
                    ->info( $log );

                // Log::info($log);
            }
        }
    加载监听 app/Providers/EventServiceProvider.php
        protected $listen = [
            Registered::class => [
                SendEmailVerificationNotification::class,
            ],
            // 新加的
            \Illuminate\Database\Events\QueryExecuted::class => [
                'App\Listeners\QueryListener'
            ],
        ];
        
    
---
### 插件安装
#### 语言包
    安装：composer require overtrue/laravel-lang:~5.0
    复制语言包：php artisan lang:publish zh-CN 
    其他语言：[zh_CN,zh_HK,th,tk]
    修改配置文件
    .env
        APP_LOCALE=zh_CN
    config/app.php
        'locale' => env("APP_LOCALE", "en"),
        //Illuminate\Translation\TranslationServiceProvider::class,
        Overtrue\LaravelLang\TranslationServiceProvider::class,
    使用
        创建语言包：resources/lang/zh_CN/demo.php
        <?php
            return [
                'user_not_exists'     => '用户不存在' ,
                'email_has_registered' => '邮箱:email 已经注册过！' ,
            ];
        trans ( 'demo.email_has_registed' , [ 'email' => 'anzhengchao@gmail.com' ]); -> 邮箱 anzhengchao@gmail.com 已经注册过！

#### 枚举
    安装：composer require bensampo/laravel-enum v4.2.0
    使用
        创建枚举文件：php artisan make:enum StatusEnum
        StatusEnum::DISABLE => array  
        StatusEnum::DISABLE['key']
        StatusEnum::DISABLE['title']

#### JWT 登录
    安装：composer require tymon/jwt-auth
    拷贝配置文件：php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
    生成密钥：php artisan jwt:secret
    配置：添加 guard：admin  member
    中间件、参考文件

#### RBAC 权限管理
    安装：composer require spatie/laravel-permission
    拷贝配置文件：php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="config"
    拷贝数据库文件：php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations"
    同步数据库
    创建、并配设置超级管理员
    修改 .env  APP_SUPER=SuperAdmin

    app/Providers/AuthServiceProvider.php
    public function boot()
    {
        $this->registerPolicies();

        // 定义超级管理员
        Gate::before(function ($admin, $ability) {
            return $admin->hasRole(env("APP_SUPER", "SuperAdmin")) ? true : null;
        });
    }

#### EasyWeChat
    安装：composer require w7corp/easywechat
    文档地址：https://www.easywechat.com/
