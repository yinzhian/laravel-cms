<?php

namespace App\Http\Services;

use App\Models\Config;
use Illuminate\Support\Facades\Cache;
use Qiniu\Auth;

class QiNiuService
{
    private string $access;
    private string $secret;
    private string $bucket;
    private string $domain;
    private string $prefix = "QN:";

    private $auth;

    public function __construct()
    {
        $this->access = (string) Config::getValue( "QINIU_ACCESS_KEY" );
        $this->secret = (string) Config::getValue( "QINIU_SECRET_KEY" );
        $this->bucket = (string) Config::getValue( "QINIU_BUCKET" );
        $this->domain = (string) Config::getValue( "QINIU_DOMAIN" );

        $this->auth = new Auth( $this->access, $this->secret );
    }

    /**
     * Notes: 获取TOKEN
     * User: 一颗地梨子
     * DateTime: 2022/3/5 15:35
     * @return array
     */
    public function getToken() : array
    {
        // 缓存的KEY
        $key = $this->prefix . "TOKEN";

        // 获取TOKEN
        if ( Cache::has( $key ) ) {
            $token = Cache::get( $key );
        } else {
            $token = $this->_getToken();
            if ( $token ) Cache::put( $key, $token, 2 * 3600 );
        }

        return ["token" => $token, "domain" => $this->domain];
    }

    /**
     * Notes: 获取上传 TOKEN
     * User: 一颗地梨子
     * DateTime: 2022/3/5 15:33
     * @return String
     */
    private function _getToken() : string
    {
        return (string) $this->auth->uploadToken( $this->bucket );
    }
}
