<?php

namespace App\Http\Services;

use EasyWeChat\Factory;
use Illuminate\Support\Facades\Config;

class WeChatService
{
    // 小程序
    private $miniApp;

    // 公众号
    private $offAccount;

    /**
     * Notes: 实例化
     * User: 一颗地梨子
     * DateTime: 2022/2/25 16:57
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function __coustruct (  )
    {
        // 小程序
        $this->miniApp = Factory::miniProgram(Config::get("wechat.miniApp"));
    }

    /**
     * Notes: 获取到 session
     * User: 一颗地梨子
     * DateTime: 2022/2/25 17:17
     * @param string $code
     * @return mixed
     */
    public function miniAppSession( string $code )
    {
        // 获取 OPEN_ID  {"session_key":"UHeSkPcEnGBuyQz2TKr42w==","openid":"oG7zs4rS_5NLGdPhUmTO_aOlKTHc","unionid":"oG7zs4rS_5NLGdPhUmTO_aOlKTHc"}
        return $this->miniApp->auth->session( $code );
    }

    /**
     * Notes: 解密
     * User: 一颗地梨子
     * DateTime: 2022/2/25 17:17
     * @param $session
     * @param $iv
     * @param $encryptedData
     * @return mixed
     */
    public function miniAppDecryptData( $session, $iv, $encryptedData )
    {
        return $this->miniApp->encryptor->decryptData( $session, $iv, $encryptedData );
    }

    /**
     * Notes: 跳转地址
     * User: 一颗地梨子
     * DateTime: 2022/2/25 17:17
     * @return mixed
     */
    public function miniAppScheme()
    {
        return $this->miniApp->url_scheme->generate();
    }
}
