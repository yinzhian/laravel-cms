<?php

namespace App\Http\Services\Union;

use App\Enums\UnionEnum;
use App\Models\Config;
use App\Models\MediumPid;
use App\Models\Member;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ZtkService extends UnionService
{
    // 联盟信息
    private array $union = UnionEnum::ALI;

    // 用户信息
    private int $member_id   = 0;  // 用户ID
    private int $member_rate = 60; // 用户佣金比例

    // 接口信息
    private string $appKey;
    private string $sid;
    private string $pid;
    private string $uri;

    private string $prefix = "ZTK:";

    public function __construct()
    {
        $this->appKey = (string) ( Config::getValue( "ZTK_APP_KEY" ));
        $this->sid    = (string) ( Config::getValue( "ZTK_SID" ));
        $this->pid    = (string) ( Config::getValue( "ZTK_PID" ));
        $this->uri    = (string) ( Config::getValue( "ZTK_URI" ));

        // 用户是否登录
        $this->member_id   = Member::getMemberId();
        $this->member_rate = Member::getMemberRate( $this->member_id );
    }

    /**
     * Notes: 获取商品
     * User: 一颗地梨子
     * DateTime: 2022-03-06 12:04
     * @return array
     */
    public function getGoods( array $search = [] ) : array
    {
        // 整理接口信息
        $api_name = ":10003/api/api_quanwang.ashx?";

        // 处理参数
        $param = array(
            "appkey"      => (string) $this->appKey,                      // 折淘客的对接秘钥appkey
            "page"        => (int) ( $search["page"] ?? 1 ),              // 页码
            "page_size"   => (int) env( "APP_PAGE", 20 ),                 // 页数
            "sort"        => (string) ( $search["sort"] ?? "new" ),       // 排序
            "q"           => (string) ( $search["word"] ?? "" ),          // 搜索词
            "material_id" => (string) ( $search["material_id"] ?? "" ),   // 官方的物料Id，默认为空表示全网商品；
            "youquan"     => (string) ( $search["coupon"] ?? "" ),        // 是否有券，1为有券，其它值为全部商品
            "tj"          => (string) ( $search["tamll"] ?? "" ),         // 是否天猫商品，值为空：全部商品，tmall：天猫商品
            "itemloc"     => (string) ( $search["city"] ?? "" ),          // 商品所在地，值为空：全部商品，其它值：北京、上海、广州、深圳、重庆、杭州等。必须是城市名称，不能带省份。
            "need_prepay" => (string) "1",                                // 是否加入消费者保障，值为空：全部商品，1：加入消费者保障 - 后期加配置文件
            "cat"         => (string) ( $search["category_id"] ?? "" ),   // 商品筛选-后台类目ID(category_id)。用,分割，最大10个，该ID可以加入折淘客开放平台API群来获取。
            "type"        => (string) "2",
        );

        // 缓存的KEY
        $cacheKey = $this->prefix . md5( http_build_query( array_filter( $param ) ) );

        if ( Cache::has( $cacheKey ) ) {
            $content = json_decode( Cache::get( $cacheKey ), true );
        } else {
            // 发送请求
            $response = $this->_http( $api_name, $param );

            // 获取结果集
            $content = ! empty( $response["content"] ) && is_array( $response["content"] ) ? $response["content"] : [];

            if ( $content ) {
                // 处理结果集
                foreach ( $content as $key => $value ) {

                    // 商品信息格式化
                    $this->goodsList["union_id"]         = $this->union["key"];
                    $this->goodsList["icon"]             = ( $value["user_type"] == $this->union["key"] ) ? $this->union["icon"] : "";
                    $this->goodsList["goods_id"]         = $value["tao_id"] ?? "";
                    $this->goodsList["goods_name"]       = $value["tao_title"] ?? "";
                    $this->goodsList["goods_carousel"]   = ! empty( $value["small_images"] ) && strstr( $value["small_images"], '|' ) ? explode( '|', $value["small_images"] ) : [ $value["pict_url"] ];
                    $this->goodsList["goods_describe"]   = ! empty( $value["tag"] ) ? urldecode( $value["tag"] ) : ( $value["jianjie"] ?? "" );
                    $this->goodsList["goods_pict"]       = $value["pict_url"] ?? "";
                    $this->goodsList["goods_sales"]      = $value["sellCount"] ?? $value["volume"] ?? 0;
                    $this->goodsList["goods_cost_price"] = $value["size"] ?? 0.00;
                    $this->goodsList["goods_price"]      = $value["quanhou_jiage"] ?? 0.00;
                    $this->goodsList["coupon_price"]     = $value["coupon_info_money"] ?? 0.00;
                    $this->goodsList["shop_title"]       = $value["shop_title"] ?? "";
                    $this->goodsList["profit"]           = (string) memberProfit( (float) $value["tkfee3"], (int) $this->member_rate );

                    // TODO 商品返回值 init
                    $content[$key] = $this->goodsList;

                }

                if ( $content && is_array( $content ) )
                    Cache::put( $cacheKey, json_encode( $content, JSON_PARTIAL_OUTPUT_ON_ERROR ), 2 * 3600 );
            }
        }

        return $content;
    }

    /**
     * Notes: 商品详情
     * User: 一颗地梨子
     * DateTime: 2022-03-06 20:23
     * @param $goods_id
     * @return array
     */
    public function getGoodsDetail( $goods_id ) : array
    {
        // TODO: Implement goodsDetail() method.
        // 整理接口信息
        $api_name = ":10002/api/api_detail.ashx?";

        // 处理参数
        $param = array(
            "appkey" => (string) $this->appKey, // 折淘客的对接秘钥appkey
            "tao_id" => (string) $goods_id,     // 页码
            "type"   => (string) "1",           //是否返回S券和G券全部数据，type=0表示返回全部两条数据，type=1表示返回S券单条数据（如无S券数据返回G券单条数据），默认type=0。
        );

        // 发送请求
        $response = $this->_http( $api_name, $param );

        if ( ! empty( $response["content"] ) && is_array( $response["content"] ) && ! empty( $response["content"][0] ) ) {

            $content = $response["content"][0];

            // 商品信息格式化
            $this->goodsDetail["union_id"]          = $this->union["key"];
            $this->goodsDetail["icon"]              = ( $content["user_type"] == $this->union["key"] ) ? $this->union["icon"] : "";
            $this->goodsDetail["goods_id"]          = $content["tao_id"];
            $this->goodsDetail["goods_name"]        = $content["tao_title"];
            $this->goodsDetail["goods_describe"]    = ! empty( $content["tag"] ) ? $content["tag"] : ( $content["jianjie"] ?? "" );
            $this->goodsDetail["goods_pict"]        = $content["pict_url"];
            $this->goodsDetail["goods_carousel"]    = ! empty( $content["small_images"] ) && strstr( $content["small_images"], '|' ) ? explode( '|', $content["small_images"] ) : [ $content["pict_url"] ];
            $this->goodsDetail["goods_detail"]      = ! empty( $content["pcDescContent"] ) && strstr( $content["pcDescContent"], '|' ) ? explode( '|', $content["pcDescContent"] ) : $this->goodsDetail["goods_carousel"];
            $this->goodsDetail["goods_detail_url"]  = $content["pcDescContent_url"] ?? "";
            $this->goodsDetail["goods_sales"]       = $content["sellCount"] ?? $content["volume"] ?? 0;
            $this->goodsDetail["goods_cost_price"]  = $content["size"];
            $this->goodsDetail["goods_price"]       = $content["quanhou_jiage"];
            $this->goodsDetail["coupon_price"]      = $content["coupon_info_money"];
            $this->goodsDetail["coupon_info"]       = $content["coupon_info"];
            $this->goodsDetail["coupon_start_time"] = $content["coupon_start_time"];
            $this->goodsDetail["coupon_end_time"]   = $content["coupon_end_time"];
            $this->goodsDetail["shop_id"]           = $content["seller_id"];
            $this->goodsDetail["shop_title"]        = $content["shop_title"];
            $this->goodsDetail["shop_logo"]         = $content["shopIcon"];
            $this->goodsDetail["city"]              = $content["provcity"];
            $this->goodsDetail["profit"]            = (string) memberProfit( (float) $content["tkfee3"], (int) $this->member_rate );
        }

        return $this->goodsDetail;
    }

    /**
     * Notes: 转链
     * User: 一颗地梨子
     * DateTime: 2022-03-08 21:11
     * @param $goods_id
     * @return array
     */
    public function toLink( $goods_id ) : array
    {

        // 获取推广位
        $mediumPid = MediumPid::getMediumPid( $this->union["key"], $this->member_id );

        // 整理接口信息
        $api_name = ":10001/api/open_gaoyongzhuanlian.ashx?";

        // 处理参数
        $param = array(
            "appkey"      => (string) $this->appKey,                 // 折淘客的对接秘钥appkey
            "sid"         => (string) $this->sid,                    // SID
            "pid"         => (string) $mediumPid->pid ?? $this->pid, // 推广位
            "num_iid"     => (string) $goods_id,                     // 商品ID
            "external_id" => (string) $this->member_id,              // 账户ID
            "signurl"     => (int) 3,                                // 返回结果整合高佣转链API、解析商品编号API，已经自动判断和拼接使用全网G券还是全网S券。
        );

        // 发送请求
        $response = $this->_http( $api_name, $param );

        // 返回值
        if ( ! empty( $response["status"] ) && ( 200 == $response["status"] ) && ( is_array( $response["content"] ) ) ) {

            $content                    = $response["content"];
            $this->goodsLink["web_url"] = $content["coupon_click_url"];
            $this->goodsLink["app_url"] = $content["shorturl"];
            $this->goodsLink["ios_url"] = $content["shorturl2"];
            $this->goodsLink["pwd"]     = $content["tkl"];
        }

        return $this->goodsLink;
    }

    /**
     * Notes: 店铺转链
     * User: 一颗地梨子
     * DateTime: 2022-03-08 21:30
     * @param $shop_id
     * @return mixed
     */
    public function toShopLink( $shop_id )
    {

        // 获取推广位
        $mediumPid = MediumPid::getMediumPid( $this->union["key"], $this->member_id );

        // 整理接口信息
        $api_name = ":10001/api/open_shop_convert.ashx?";

        // 处理参数
        $param = array(
            "appkey"    => (string) $this->appKey,                 // 折淘客的对接秘钥appkey
            "sid"       => (string) $this->sid,                    // SID
            "site_id"   => (string) $mediumPid->pid ?? $this->pid, // 	备案的网站id, mm_xx_xx_xx pid三段式中的第二段
            "fields"    => (string) "user_id,click_url",           // 需返回的字段列表，如：user_id,click_url
            "user_ids"  => (string) $shop_id,                      // 	卖家ID串，用','分割，可通过全网商品详情API接口获得,seller_id字段
            "platform"  => (string) "2",                           // 链接形式：1：PC，2：无线，默认：1
            "adzone_id" => (string) $this->member_id,              // 广告位ID，区分效果位置
            "unid"      => (string) $this->member_id,              // 自定义输入串，英文和数字组成，长度不能大于12个字符，区分不同的推广渠道
            "signurl"   => (int) 0,                                // 值为1或者2，表示返回淘宝联盟请求地址，大家拿到地址后再用自己的服务器二次请求即可获得最终结果，值为1返回http链接，值为2返回https安全链接，值为0表示直接返回最终结果。
        );

        // 发送请求
        $response = $this->_http( $api_name, $param );

        // 返回值
        $content = $response["tbk_sc_shop_convert_response"]["results"]["n_tbk_shop"];

        return $content;
    }

    /**
     * Notes: 热门搜索词
     * User: 一颗地梨子
     * DateTime: 2022/3/8 10:16
     * @return array
     */
    public function getHotWord()
    {
        // 整理接口信息
        $api_name = ":10001/api/api_guanjianci.ashx?";

        // 处理参数
        $param = array(
            "appkey"    => (string) $this->appKey,                      // 折淘客的对接秘钥appkey
            "page"      => (int) ( $search["page"] ?? 1 ),              // 页码
            "page_size" => (int) env( "APP_PAGE", 20 ),                 // 页数
            "type"      => (string) "1",
        );

        $response = $this->_http( $api_name, $param );

        // 返回值
        $result = [];

        if ( ! empty( $response["status"] ) && ( 200 == $response["status"] ) && ( is_array( $response["content"] ) ) ) {
            foreach ( $response["content"] as $key => $value ) {
                $result[$key]["word"] = $value["keywords"];
            }
        }

        return $result;
    }

    public function getCarousel()
    {
        // 整理接口信息
        $api_name = ":10001/api/api_lunbo.ashx?";

        // 处理参数
        $param = array(
            "appkey"    => (string) $this->appKey,                 // 折淘客的对接秘钥appkey
            "page"      => (int) ( $search["page"] ?? 1 ),         // 页码
            "page_size" => (int) env( "APP_PAGE", 20 ),            // 页数
        );

        $response = $this->_http( $api_name, $param );

        // 返回值
        $result = [];

        if ( ! empty( $response["status"] ) && ( 200 == $response["status"] ) && ( is_array( $response["content"] ) ) ) {
            foreach ( $response["content"] as $key => $value ) {
                $result[$key]["word"] = $value["keywords"];
            }
        }

        return $result;
    }

    /**
     * Notes: 发送请求
     * User: 一颗地梨子
     * DateTime: 2022/3/5 18:19
     * @param String $api_name
     * @param array $param
     * @return array
     */
    private function _http( string $api_name, array $param ) : array
    {

        // 整理请求地址
        $url = $this->uri . $api_name . http_build_query( $param );

        // 发送
        $response = Http::get( $url );

        // 是否成功
        if ( $response->successful() ) {

            // 处理结果集
            $response = json_decode( $response->body(), true );

            // 记录日志
            diyLog( "折淘客 接口地址：{$url}", $response, "ztk" );
        }

        return $response;
    }
}
