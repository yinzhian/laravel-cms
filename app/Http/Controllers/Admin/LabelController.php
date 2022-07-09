<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LabelTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommonRequest;
use App\Http\Requests\LabelRequest;
use App\Models\Label;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    /**
     * Notes: 列表
     * User: 一颗地梨子
     * DateTime: 2022/3/14 17:46
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index( Request $request )
    {
        return $this->ok( Label::list( $request ) );
    }

    /**
     * Notes: 全部
     * User: 一颗地梨子
     * DateTime: 2022/3/23 18:01
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll( Request $request )
    {
        // 参数
        $type = $request->get( "type", LabelTypeEnum::ARTICLE["key"] );

        $labels = Label::getAll( $type );

        return $this->ok( $labels );
    }

    /**
     * Notes: 详情
     * User: 一颗地梨子
     * DateTime: 2022/3/14 17:53
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail( $id )
    {
        return $this->ok( Label::detail( (int) $id ) );
    }

    /**
     * Notes: 添加
     * User: 一颗地梨子
     * DateTime: 2022/3/14 18:03
     * @param LabelRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create( LabelRequest $request )
    {
        if ( ! Label::insert( $request->all() ) ) {
            return $this->fail();
        }

        return $this->ok();
    }

    /**
     * Notes: 更新
     * User: 一颗地梨子
     * DateTime: 2022/3/14 18:04
     * @param LabelRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update( LabelRequest $request, $id )
    {
        if ( ! Label::where( "id", $id )->update( $request->all() ) ) {
            return $this->fail();
        }
        return $this->ok();
    }

    /**
     * Notes: 删除
     * User: 一颗地梨子
     * DateTime: 2022/3/14 18:04
     * @param CommonRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete( CommonRequest $request )
    {
        if ( ! Label::whereIn( "id", $request->ids )->delete() ) {

            return $this->fail();
        }
        return $this->ok();
    }

    /**
     * Notes: 还原
     * User: 一颗地梨子
     * DateTime: 2022/3/22 11:29
     * @param CommonRequest $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function restore( CommonRequest $request )
    {
        if ( ! Label::whereIn( "id", $request->ids )->restore() ) {
            return $this->fail();
        }
        return $this->ok();
    }
}
