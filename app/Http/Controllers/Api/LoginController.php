<?php

namespace App\Http\Controllers\Api;

use App\Enums\LoginTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Services\WeChatService;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    /**
     *  微信
     * @var WeChatService
     */
    private WeChatService $weChat;

    /**
     * LoginController constructor.
     * @param WeChatService $weChatService
     */
    public function __construct( WeChatService $weChatService )
    {
        $this->weChat = $weChatService;
    }

    /**
     * Notes: code 登录 解析 openid
     * User: 一颗地梨子
     * DateTime: 2022/2/25 18:12
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function code( LoginRequest $request )
    {

        // code 登录
        $session = $this->weChat->miniAppSession( $request->get( "code" ) );

        if (
            ! $session ||
            ! is_array( $session ) ||
            empty( $session["openid"] ) ||
            empty( $session["session_key"] ) ) {
            return $this->fail( "登录失败", "401" );
        }

        // 整理返回值
        $result = [
            "openid"  => trim( $session["openid"] ),
            "unionid" => trim( $session["unionid"] )
        ];

        // 如果存在、就删除
        if ( Cache::has($result["openid"]) ) Cache::forget($result["openid"]);

        // 存储缓存
        Cache::put( $result["openid"], trim( $session["session_key"] ), 2 * 3600 ); // 存 2小时

        return $this->ok( $result );
    }

    /**
     * Notes: 登录
     * User: 一颗地梨子
     * DateTime: 2022/3/2 17:50
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function login( LoginRequest $request )
    {
        // 参数
        $params = $request->all();

        if ( $params["login_type"] == LoginTypeEnum::WECHAT_MINI_APP["key"] ) {
            // 获取 Session_key
            $session_key = Cache::pull( $params["openid"] );

            if ( !$session_key ) return $this->fail( "请登录", 401, 401 );

            // 解析手机号
            $phone_info = $this->weChat->miniAppDecryptData( $session_key, $params["iv"], $params["data"] );

            // 手机号
            $params["phone"] = $phone_info['purePhoneNumber'] ?? $phone_info['phoneNumber'] ?? ''; // 获取手机号

            if ( !$params["phone"] ) return $this->fail( "请登录", 401, 401 );
        } else if ( $params["login_type"] == LoginTypeEnum::SMS_CODE["key"] ) {
            // 获取验证码 - 自己处理
            if ( 888888 != $params["password"] ) {
                return $this->fail("验证码错误", 402);
            }
        }

        // 开始登录
        $member = Member::login($params);

        // 生成 Token
        $token = auth( "member" )->tokenById( $member["id"] );

        if ( !$token ) return $this->fail("登陆失败", 401);

        $data = array(
            "token"      => "bearer " . $token,
            'expires_in' => auth( 'member' )->factory()->getTTL() * 60
        );

        return $this->ok( $data );

    }

    public function demoLogin (  )
    {
        // 生成 Token
        $token = auth( "member" )->tokenById( 1 );

        if ( !$token ) return $this->fail("登陆失败", 401);

        $data = array(
            "token"      => "bearer " . $token,
            'expires_in' => auth( 'member' )->factory()->getTTL() * 60
        );

        return $this->ok( $data );
    }

}
