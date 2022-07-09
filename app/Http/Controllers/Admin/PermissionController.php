<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommonRequest;
use App\Http\Requests\PermissionRequest;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Notes: 列表
     * User: 一颗地梨子
     * DateTime: 2022/2/17 11:41
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index( Request $request )
    {
        return $this->ok( Permission::list( $request ) );
    }

    /**
     * Notes: 获取全部
     * User: 一颗地梨子
     * DateTime: 2022/2/18 15:35
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll( Request $request )
    {
        return $this->ok( Permission::getAll( $request->get( "parent_id", 0 ) ) );
    }

    /**
     * Notes: 详情
     * User: 一颗地梨子
     * DateTime: 2022/2/17 11:44
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail( $id )
    {
        return $this->ok( Permission::detail( $id ) );
    }

    /**
     * Notes: 添加
     * User: 一颗地梨子
     * DateTime: 2022/2/17 14:58
     * @param PermissionRequest $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function create( PermissionRequest $request )
    {
        $params = $request->all();

        if ( ! Permission::insert( $params ) ) {
            return $this->fail();
        }

        return $this->ok();
    }

    /**
     * Notes: 更新
     * User: 一颗地梨子
     * DateTime: 2022/2/17 15:05
     * @param PermissionRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function update( PermissionRequest $request, $id )
    {
        $params = $request->all();

        if ( ! Permission::where( "id", $id )->update( $params ) ) {
            return $this->fail();
        }

        return $this->ok();
    }

    /**
     * Notes: 软删除
     * User: 一颗地梨子
     * DateTime: 2022/2/17 15:07
     * @param CommonRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete( CommonRequest $request )
    {
        if ( ! Permission::whereIn( "id", $request->ids )->delete()  ) {
            return $this->fail();
        }

        return $this->ok();
    }

    /**
     * Notes: 还原
     * User: 一颗地梨子
     * DateTime: 2022/3/22 11:28
     * @param CommonRequest $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function restore( CommonRequest $request )
    {
        if ( ! Permission::whereIn( "id", $request->ids )->restore() ) {
            return $this->fail();
        }
        return $this->ok();
    }
}
