<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
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
        return $this->ok( Role::list( $request ) );
    }

    /**
     * Notes: 获取全部
     * User: 一颗地梨子
     * DateTime: 2022/2/18 15:35
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        return $this->ok( Role::getAll() );
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
        return $this->ok( Role::detail( $id ) );
    }

    /**
     * Notes: 添加
     * User: 一颗地梨子
     * DateTime: 2022/2/17 14:58
     * @param RoleRequest $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function create( RoleRequest $request )
    {
        $params = $request->all();

        if ( ! Role::insert( $params ) ) {
            return $this->fail();
        }

        return $this->ok();
    }

    /**
     * Notes: 更新
     * User: 一颗地梨子
     * DateTime: 2022/2/17 15:05
     * @param RoleRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function update( RoleRequest $request, $id )
    {
        $params = $request->all();

        if ( ! Role::where( "id", $id )->update( $params ) ) {

            return $this->fail();
        }

        return $this->ok();
    }

    /**
     * Notes: 软删除
     * User: 一颗地梨子
     * DateTime: 2022/2/17 15:07
     * @param RoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete( RoleRequest $request )
    {

        if ( ! Role::whereIn( "id", $request->get("ids") )->delete() ) {
            return $this->fail();
        }

        return $this->ok();
    }

    /**
     * Notes: 还原
     * User: 一颗地梨子
     * DateTime: 2022/3/22 11:23
     * @param RoleRequest $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function restore( RoleRequest $request )
    {
        if ( !Role::whereIn( "id", $request->get("ids") )->restore() ) {
            return $this->fail();
        }
        return $this->ok();
    }

    /**
     * Notes: 角色授权
     * User: 一颗地梨子
     * DateTime: 2022/2/19 10:31
     * @param RoleRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function empower( RoleRequest $request, $id )
    {
        // 参数 数组:[1,2,3]
        $permission_ids = $request->get( "permission_ids" );

        $role = Role::find( $id );

        if ( ! $role ) {
            return $this->fail( "账号不存在" );
        }

        // 一次性撤销和添加新权限
        $role->syncPermissions( $permission_ids );

        return $this->ok();
    }
}
