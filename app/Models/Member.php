<?php

namespace App\Models;

use App\Enums\IdentityEnum;
use App\Enums\LoginTypeEnum;
use App\Enums\SexEnum;
use App\Enums\SourceEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Member extends Authenticatable implements JWTSubject
{
    use HasFactory;

    use SoftDeletes;

    protected function serializeDate( \DateTimeInterface $date ) : string
    {
        return $date->format( "Y-m-d H:i:s" );
    }

    /**
     * Notes: 手机号
     * User: 一颗地梨子
     * DateTime: 2022/3/3 14:45
     * @return string
     */
    public function getPhoneZhAttribute() : string
    {
        return (string) phoneSecrecy( $this->phone );
    }

    /**
     * Notes: 身份
     * User: 一颗地梨子
     * DateTime: 2022/3/3 14:45
     * @return string
     */
    public function getIdentityZhAttribute() : string
    {
        return (string) IdentityEnum::getTitle( $this->identity );
    }

    /**
     * Notes: 来源
     * User: 一颗地梨子
     * DateTime: 2022/3/3 14:45
     * @return string
     */
    public function getSourceZhAttribute() : string
    {
        return (string) SourceEnum::getTitle( $this->source );
    }

    /**
     * Notes: 性别
     * User: 一颗地梨子
     * DateTime: 2022/3/3 14:45
     * @return string
     */
    public function getSexZhAttribute() : string
    {
        return (string) SexEnum::getTitle( $this->sex );
    }

    public function getJWTIdentifier()
    {
        // TODO: Implement getJWTIdentifier() method.
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        // TODO: Implement getJWTCustomClaims() method.
        return [ 'role' => 'member' ];
    }

    /**
     * Notes: 执行登录
     * User: 一颗地梨子
     * DateTime: 2022/3/2 17:38
     * @param array $params
     * @return array|mixed
     */
    static function login( array $params )
    {

        // 获取登录凭证
        $member = self::getMemberByPhone( $params["phone"] );

        if ( ! $member ) {

            // 注册
            $member = self::register( $params );

        }

        // 添加凭证
        if ( LoginTypeEnum::SMS_CODE["key"] == $params["login_type"] ) {

            // 校验短信验证码

        } else {
            $params["member_id"] = $member["id"];
            MemberVoucher::addVoucher( $params );
        }

        return $member;
    }

    /**
     * Notes: 注册
     * User: 一颗地梨子
     * DateTime: 2022/3/2 17:45
     * @param array $params
     * @return array
     */
    static function register( array $params )
    {
        // 处理 parent
        $parent = ! empty( $params["invite_code"] ) ? self::getParent( $params["invite_code"] ) : "";

        // 组织用户数据
        $member = array(
            "parent_id"   => $parent->id ?? 0,
            "agent_id"    => $parent->agent_id ?? 0,
            "phone"       => $params["phone"] ?? "",
            "nick"        => $params["nick"] ?? phoneSecrecy( $params["phone"] ),
            "avatar"      => $params["avatar"] ?? Config::getValue( "DEGAULT_AVATAR" ),
            "invite_code" => self::makeInviteCode(),
        );

        // 添加用户
        $member["id"] = $params["member_id"] = self::insertGetId( $member );

        if ( $params["member_id"] ) {

            // 处理用户的路径关系
            $parentPath = $parent->path ?? "";
            $path       = $parentPath ? $member["id"] . ',' . $parentPath : $member["id"];
            self::where( "id", $member["id"] )->update( [ 'path' => $path ] );

        }

        return $member;
    }

    /**
     * Notes: 生成邀请码
     * User: 一颗地梨子
     * DateTime: 2022/3/2 16:34
     * @return String
     */
    static private function makeInviteCode() : string
    {

        // 生成随机字符串
        $invite_code = strtoupper( Str::random( rand( 6, 8 ) ) );

        // 验重
        if ( self::getParent( $invite_code ) ) self::makeInviteCode();

        return $invite_code;
    }

    /**
     * Notes: 根据邀请码获取 parent
     * User: 一颗地梨子
     * DateTime: 2022/3/2 15:52
     * @param String $invite_code
     * @return mixed
     */
    static function getParent( string $invite_code )
    {

        return self::where( "invite_code", $invite_code )
                   ->select( "id", "path", "agent_id" )
                   ->first();

    }

    /**
     * Notes: 获取用户ID
     * User: 一颗地梨子
     * DateTime: 2022/3/3 14:34
     * @return int
     */
    static function getMemberId() : int
    {
        return (int) auth()->id();
    }

    /**
     * Notes: 手机号查询用户信息
     * User: 一颗地梨子
     * DateTime: 2022-02-26 17:29
     * @param $phone
     * @return mixed
     */
    static function getMemberByPhone( $phone )
    {
        return self::where( "phone", $phone )
                   ->select( "id", "parent_id", "agent_id", "nick", "phone", "avatar", "invite_code",
                             "path", "birthday", "identity", "sex", "status", "created_at", 'updated_at', 'deleted_at' )
                   ->first();
    }

    /**
     * Notes: 获取用户信息
     * User: 一颗地梨子
     * DateTime: 2022/3/3 14:39
     * @return mixed
     */
    static function getMember()
    {

        return self::where( "id", self::getMemberId() )
                   ->select( "id", "phone", "nick", "avatar", "invite_code", "birthday", "identity", "sex" )
                   ->first()
                   ->makeHidden( [ 'phone' ] )
                   ->append( [ 'phone_zh', 'identity_zh', 'sex_zh' ] );

    }
}
