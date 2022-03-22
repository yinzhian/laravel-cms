<?php

namespace App\Http\Services\Union;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

/**
 * Notes: 联盟统一接口
 * User: 一颗地梨子
 * DateTime: 2022-03-06 20:27
 */
abstract class UnionService
{

    /// TODO 初始化商品列表信息
    protected array $goodsList
        = [
            "union_id"         => 0,    // 联盟ID
            "icon"             => "",   // 图标
            "goods_id"         => "",   // 商品ID
            "goods_name"       => "",   // 商品名称
            "goods_carousel"   => [],   // 商品轮播
            "goods_describe"   => "",   // 商品描述
            "goods_pict"       => "",   // 商品主图
            "goods_sales"      => 0,    // 销量
            "goods_cost_price" => 0.00, // 原价
            "goods_price"      => 0.00, // 售价 = 原价 - 优惠券金额
            "coupon_price"     => 0.00, // 优惠券金额
            "profit"           => 0.00, // 预计收益
            "shop_title"       => "",   // 店名
        ];

    /// TODO 初始化商品详情信息
    protected array $goodsDetail
        = [
            "union_id"          => 0,    // 联盟ID
            "icon"              => "",   // 图标
            "goods_id"          => "",   // 商品ID
            "goods_name"        => "",   // 商品名称
            "goods_describe"    => "",   // 商品描述
            "goods_pict"        => "",   // 商品主图
            "goods_carousel"    => [],   // 商品轮播
            "goods_detail"      => [],   // 商品详情
            "goods_detail_url"  => "",   // 商品详情链接
            "goods_sales"       => 0,    // 销量
            "goods_cost_price"  => 0.00, // 原价
            "goods_price"       => 0.00, // 售价 = 原价 - 优惠券金额
            "coupon_price"      => 0.00, // 优惠券金额
            "coupon_info"       => "",   // 优惠券信息 满100-20
            "coupon_start_time" => "",   // 优惠券开始时间
            "coupon_end_time"   => "",   // 优惠券结束时间
            "profit"            => 0.00, // 预计收益
            "shop_id"           => "",   // 店铺ID
            "shop_title"        => "",   // 店名
            "shop_logo"         => "",   // 店铺LOGO
            "city"              => "",   // 所在地
        ];

    /// TODO 初始化商品转链结果
    protected array $goodsLink
        = [
            "web_url" => "", // WEB 打开链接
            "app_url" => "", // APP 打开地址
            "ios_url" => "", // APP 打开地址 - 调起IOS
            "pwd"     => "", // 口令
        ];

    /// TODO 订单字段
    protected array $orderData
        = [
            "member_id"          => 0, // 用户ID
            "union_id"           => 0, // 联盟ID
            "medium_id"          => 0, // 媒体ID
            "medium_pid_id"      => 0, // 推广位ID
            "order_type"         => "淘宝", // 订单类型
            "order_no"           => "", // 订单号 - 用户可见
            "main_order_no"      => "", // 主订单号 - 用户不可见
            "order_price"        => "", // 订单金额 - 参与返利
            "order_rate"         => "", // 返利比例
            "order_profit"       => "", // 预计佣金
            "order_service_rate" => "", // 订单服务费比例
            "goods_id"           => "", // 商品ID
            "goods_title"        => "", // 商品名称
            "goods_pict"         => "", // 商品主图
            "goods_url"          => "", // 商品链接
            "goods_price"        => "", // 商品价格
            "goods_num"          => 1, // 商品数量
            "shop_title"         => "", // 店铺名
            "status"             => OrderStatusEnum::INVALID["key"], // 状态
            "click_at"           => "", // 点击时间
            "invalid_at"         => "", // 失效时间
            "played_at"          => "", // 付款时间
            "take_at"            => "", // 收货时间
            "settlement_at"      => "", // 结算时间 - 联盟结算时间 - 对平台
            "success_at"         => "", // 完成时间 - 平台结算时间 - 对用户
        ];

    /// TODO 获取商品列表
    abstract public function getGoods( array $search = [] ) : array;

    /// TODO 商品详情
    abstract public function getGoodsDetail( $goods_id ) : array;

    /// TODO 商品转链
    abstract public function toLink( $goods_id ) : array;
}
