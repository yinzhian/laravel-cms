<?php

namespace App\Http\Services\Union;

use App\Enums\OrderStatusEnum;
use App\Enums\UnionEnum;
use App\Models\Config;
use App\Models\Medium;
use App\Models\MediumPid;
use App\Models\Member;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Topsdk\Topapi\Ability414\Ability414;
use Topsdk\Topapi\Ability414\Request\TaobaoTbkOrderDetailsGetRequest;
use Topsdk\Topapi\TopApiClient;

class AliService extends UnionService
{

    // 联盟信息
    private array $union = UnionEnum::ALI;

    // 接口密钥
    private string $appKey;
    private string $appSecret;
    private string $uri;

    /**
     *  订单状态
     * @var array
     *
     * 已拍下：指订单已拍下，但还未付款
     * 已付款：指订单已付款，但还未确认收货
     * 已收货：指订单已确认收货，但商家佣金未支付
     * 已结算：指订单已确认收货，且商家佣金已支付成功
     * 已失效：指订单关闭/订单佣金小于0.01元
     * 订单关闭主要有：
     *      1）买家超时未付款；
     *      2）买家付款前，买家/卖家取消了订单；
     *      3）订单付款后发起售中退款成功；
     * 3：订单结算，
     * 11：拍下未付款，
     * 12：订单付款，
     * 13：订单失效，
     * 14：订单成功
     *
     */
    private array $tkStatus
        = array(
            3  => OrderStatusEnum::TAKE["key"],
            11 => OrderStatusEnum::NO_PLAY["key"],
            12 => OrderStatusEnum::PLAYED["key"],
            13 => OrderStatusEnum::INVALID["key"],
            14 => OrderStatusEnum::SETTLEMENT["key"],
        );

    // 接口工具类
    private $client;

    // 用户信息
    private int $member_id   = 0;  // 用户ID
    private int $member_rate = 60; // 用户佣金比例

    // 缓存KEU
    private string $cacheKey = "ORDER_DIFF:ALI";

    public function __construct()
    {
        //初始化配置信息
        $this->appKey    = (string) ( Config::getValue( "ALI_APP_KEY" ) ?? env( "ALI_APP_KEY" ) );
        $this->appSecret = (string) ( Config::getValue( "ALI_APP_SECRET" ) ?? env( "ALI_APP_SECRET" ) );
        $this->uri       = (string) ( Config::getValue( "ALI_APP_URI" ) ?? env( "ALI_APP_URI" ) );

        // 实例化接口
        $this->client = new TopApiClient( $this->appKey, $this->appSecret, $this->uri );

        // 用户是否登录
        $this->member_id   = Member::getMemberId();
        $this->member_rate = Member::getMemberRate( $this->member_id );
    }


    public function getGoods( array $search = [] ) : array
    {
        // TODO: Implement getGoods() method.

        return $this->goodsList;
    }

    public function getGoodsDetail( $goods_id ) : array
    {
        // TODO: Implement getGoodsDetail() method.

        return $this->goodsDetail;
    }

    public function toLink( $goods_id ) : array
    {
        // TODO: Implement toLink() method.

        return $this->goodsLink;
    }

    /**
     * Notes: 定时器同步订单 - 每分钟执行一次
     * User: 一颗地梨子
     * DateTime: 2022-03-18 22:08
     * @param array $search
     */
    public function order( $search = [] )
    {
        // 重构参数
        $search["page_no"]        = (int) ( $search["page_no"] ?? 1 );   // 第几页，默认1，1~100
        $search["jump_type"]      = (int) ( $search["jump_type"] ?? 1 ); // 跳转类型，当向前或者向后翻页必须提供,-1: 向前翻页,1：向后翻页
        $search["position_index"] = $search["position_index"] ?? "";     // 位点，除第一页之外，都需要传递；前端原样返回。

        // 处理起止时间
        if ( ! ( isset( $search["start_time"] ) && isset( $search["end_time"] ) ) ) {
            // 获取时间
            $start_time           = $search["start_time"] ?? "";
            $times                = $this->_handleTime( $start_time );
            $search["start_time"] = $times["start_time"];
            $search["end_time"]   = $times["end_time"];
        }

        // 实例化
        $ability = new Ability414( $this->client );

        // 整理参数
        $request = new TaobaoTbkOrderDetailsGetRequest();
        $request->setPageNo( $search["page_no"] );                 // 第几页，默认1，1~100
        $request->setPageSize( env( "APP_PAGE", 20 ) ); // 页大小，默认20，1~100
        $request->setJumpType( $search["jump_type"] );             // 跳转类型，当向前或者向后翻页必须提供,-1: 向前翻页,1：向后翻页
        $request->setPositionIndex( $search["position_index"] );  // 位点，除第一页之外，都需要传递；前端原样返回。
        $request->setOrderScene( 1 );                   // 场景订单场景类型，1:常规订单，2:渠道订单，3:会员运营订单，默认为1
        $request->setQueryType( 2 );                     // 查询时间类型，1：按照订单淘客创建时间查询，2:按照订单淘客付款时间查询，3:按照订单淘客结算时间查询，4:按照订单更新时间；
        $request->setEndTime( date("Y-m-d H:i:00", strtotime($search["end_time"])) );    // 订单查询结束时间，订单开始时间至订单结束时间，中间时间段日常要求不超过3个小时，但如618、双11、年货节等大促期间预估时间段不可超过20分钟，超过会提示错误，调用时请务必注意时间段的选择，以保证亲能正常调用！
        $request->setStartTime( date("Y-m-d H:i:00", strtotime($search["start_time"])) ); // 订单查询开始时间
        //$request->setMemberType( 2 );                            // 推广者角色类型,2:二方，3:三方，不传，表示所有角色
        //$request->setTkStatus( 12 );                             // 淘客订单状态，11-拍下未付款，12-付款，13-关闭，14-确认收货，3-结算成功;不传，表示所有状态

        try {

            // 获取结果集 - 返回的是一个对象
            $response = $ability->taobaoTbkOrderDetailsGet( $request );

            // 处理结果集
            $results = $response->data->results ?? "";

            if ( $results ) {
                // 订单信息
                $orders = $results->publisher_order_dto ?? [];

                if ( $orders && is_array( $orders ) ) {

                    // 全强制处理为 二维数组
                    $orders = empty( $orders[0] ) ? [ $orders ] : $orders;

                    foreach ( $orders as $key => $order ) {

                        // 推广位信息
                        $mediumPid = MediumPid::getMemberId( (int) $this->union["key"], (string) $order->adzone_name );

                        // 用户ID
                        $this->member_id = $mediumPid->member_id ?? 0;

                        // 处理状态
                        $status = $this->tkStatus[$order->tk_status] ?? OrderStatusEnum::INVALID["key"];

                        // 用户信息
                        $this->orderData["member_id"]          = $this->member_id;
                        $this->orderData["union_id"]           = $this->union["key"];
                        $this->orderData["medium_id"]          = Medium::getDetailByTitle( $order->site_id )->id ?? 0;
                        $this->orderData["medium_pid_id"]      = $mediumPid->id ?? 0;
                        $this->orderData["order_type"]         = $order->order_type ?? "淘宝";
                        $this->orderData["order_no"]           = $order->trade_parent_id;                                                                       // 订单号
                        $this->orderData["main_order_no"]      = $order->trade_id;                                                                              // 主订单号
                        $this->orderData["order_price"]        = $order->alipay_total_price;                                                                    // 订单付款金额
                        $this->orderData["order_rate"]         = (int) ( $order->total_commission_rate ?? $order->tk_total_rate ?? $order->income_rate );       // 订单佣金比例
                        $this->orderData["order_profit"]       = $order->pub_share_pre_fee;                                                                     // 订单预计佣金
                        $this->orderData["order_service_rate"] = $order->alimama_rate ?? 0;                                                                     // 订单服务费比例
                        $this->orderData["goods_id"]           = $order->item_id;                                                                               // 商品ID
                        $this->orderData["goods_title"]        = $order->item_title;                                                                            // 商品标题
                        $this->orderData["goods_pict"]         = Str::is('http*', $order->item_img) ? $order->item_img : (Str::is('//*', $order->item_img) ? "https:".$order->item_img : "https://".$order->item_img); // 商品主图
                        $this->orderData["goods_url"]          = $order->item_link;                                                                             // 商品链接
                        $this->orderData["goods_price"]        = $order->item_price;                                                                            // 商品价格
                        $this->orderData["goods_num"]          = $order->item_num;                                                                              // 商品数量
                        $this->orderData["shop_title"]         = $order->seller_shop_title;                                                                     // 店铺名
                        $this->orderData["status"]             = $status;                                                                                       // 状态
                        $this->orderData["click_at"]           = $order->click_time ?? "";                                                                      // 点击时间

                        // 处理时间
                        switch ( $status ) {
                            case OrderStatusEnum::INVALID["key"]:
                                $this->orderData["invalid_at"] = $order->modified_time ?? date( "Y-m-d H:i:s" ); // 失效时间
                                break;
                            case OrderStatusEnum::PLAYED["key"]:
                                $this->orderData["played_at"] = $order->tb_paid_time ?? $order->tk_paid_time ?? ""; // 失效时间
                                break;
                            case OrderStatusEnum::TAKE["key"]:
                                $this->orderData["take_at"] = $order->tk_earning_time ?? $order->modified_time ?? ""; // 确认收货
                                break;
                            case OrderStatusEnum::SETTLEMENT["key"]:
                                $this->orderData["settlement_at"] = $order->tk_earning_time ?? $order->modified_time ?? ""; // 结算时间 - 联盟结算时间 - 对平台
                                break;
                            case OrderStatusEnum::SUCCESS["key"]:
                                $this->orderData["success_at"] = date( "Y-m-d H:i:s" ); // 完成时间
                                break;
                        }

                        // 订单入库
                        Order::orderCreate( $this->orderData );

                    }

                }
            }

            /// TODO 处理后续操作
            if ( $response->data->has_next ) {
                // 有下一页
                $search["page_no"]        = (int) ( $response->data->page_no + 1 ); // 第几页，默认1，1~100
                $search["position_index"] = $response->data->position_index;        // 位点，除第一页之外，都需要传递；前端原样返回。

                // 回调 - 获取下一页
                $this->order( $search );
            } else if ( ( time() - strtotime( $search["end_time"] ) ) > ( Config::getValue( "ORDER_END_TIME_DIFF", 5 ) * 60 ) ) {
                // 如果结束时间不是当前时间、则继续再调一次
                // 初始化页数
                $search["page_no"]        = (int) 1;   // 第几页，默认1，1~100
                $search["position_index"] = "";        // 位点，除第一页之外，都需要传递；前端原样返回。

                // 将开始时间设置为上次的结束时间
                $search["start_time"] = $search["end_time"];
                $search["end_time"]   = "";
            } else {
                // 缓存本次接口结束时间 - 缓存 20分钟
                Cache::put( $this->cacheKey, $search["end_time"], 20 * 60 );
            }

        } catch ( \Exception $exception ) {
            // 执行异常 - 记录该异常时间段、通知管理员


        }

        dd( $response );
    }

    /**
     * Notes: 处理订单时间
     * User: 一颗地梨子
     * DateTime: 2022/3/16 17:20
     * @param string $start_time
     * @return string[]
     *
     * TODO 如果开始时间和结束均不存在，则结束时间取当前时间、开始时间提前20分钟
     * TODO 如果开始时间存在、结束时间不存在，则开始时间提前2分钟、结束时间据当前时间如果小于20分钟、则取当前时间、否则取开始时间后20分钟
     * TODO 如果开始时间结束时间均存在，则不做任何处理 - 获取下一页
     * TODO 每次请求完成之后，将结束时间存入缓存，作为下一次的开始时间；如果出现报错，则需要记录报错时间段、存入数据库，并通知管理员
     *
     */
    private function _handleTime( $start_time = "" ) : array
    {

        // 返回值
        $times = [
            "start_time" => $start_time, // 开始时间
            "end_time"   => date( "Y-m-d H:i:00" ), // 结束时间
        ];

        // 订单时间差
        $aliOrderDiffTime = Config::getValue("ALI_ORDER_DIFF_TIME", 20);

        if ( ! $times["start_time"] ) {

            // 在缓存中取上次结束的时间 - 如果没有、则直接取当前时间的前二十分钟
            if ( Cache::has( $this->cacheKey ) ) {
                $times["start_time"] = Cache::pull( $this->cacheKey ); // 开始时间
            } else {
                $times["start_time"] = date( "Y-m-d H:i:00", time() - ( $aliOrderDiffTime * 60 ) );
            }
        }

        // 计算起止时间差
        $diff = time() - strtotime( $times["start_time"] );
        if ( $diff > ( $aliOrderDiffTime * 60 ) ) {
            $times["end_time"] = date( "Y-m-d H:i:00", strtotime( $times["start_time"] ) + ( $aliOrderDiffTime * 60 ) );
        }

        return $times;
    }
}
