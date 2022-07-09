<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\CommonRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Notes: 获取个人信息
     * User: 一颗地梨子
     * DateTime: 2022/2/18 11:08
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->ok( Admin::getAdmin() );
    }

    /**
     * Notes: 列表
     * User: 一颗地梨子
     * DateTime: 2022/2/17 11:41
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index( Request $request )
    {
        return $this->ok( Admin::list( $request ) );
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
        return $this->ok( Admin::detail( $id ) );
    }

    /**
     * Notes: 添加
     * User: 一颗地梨子
     * DateTime: 2022/2/17 14:58
     * @param AdminRequest $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function create( AdminRequest $request )
    {
        $params = $request->all();

        $params["password"] = Hash::make( trim( $params["password"] ) );

        if ( ! Admin::insert( $params ) ) {
            return $this->fail();
        }

        return $this->ok();
    }

    /**
     * Notes: 更新
     * User: 一颗地梨子
     * DateTime: 2022/2/17 15:05
     * @param AdminRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function update( AdminRequest $request, $id )
    {
        $params = $request->all();

        if ( ! empty( $params["password"] ) ) {
            $params["password"] = Hash::make( trim( $params["password"] ) );
        }

        if ( ! Admin::where( "id", $id )->update( $params ) ) {
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
        if ( ! Admin::whereIn( "id", $request->ids )->delete() ) {
            return $this->fail();
        }
        return $this->ok();
    }

    /**
     * Notes: 还原删除
     * User: 一颗地梨子
     * DateTime: 2022/2/17 15:07
     * @param CommonRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore( CommonRequest $request )
    {
        if ( ! Admin::whereIn( "id", $request->ids )->restore() ) {
            return $this->fail();
        }
        return $this->ok();
    }

    /**
     * Notes: 管理员添加角色
     * User: 一颗地梨子
     * DateTime: 2022/2/18 17:57
     * @param AdminRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function addRole( AdminRequest $request, $id )
    {
        // 参数 数组:[1,2,3]
        $role_ids = $request->role_ids;

        $admin = Admin::find( $id );

        if ( ! $admin ) {
            return $this->fail( "账号不存在" );
        }

        // 删除原角色、添加新角色
        $admin->syncRoles( $role_ids );

        return $this->ok();
    }

    /**
     * Notes: 管理员 - 授权
     * User: 一颗地梨子
     * DateTime: 2022/2/18 17:57
     * @param AdminRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function empower( AdminRequest $request, $id )
    {
        // 参数 数组:[1,2,3]
        $permission_ids = $request->permission_ids;

        $admin = Admin::find( $id );

        if ( ! $admin ) {
            return $this->fail( "账号不存在" );
        }

        // 一次性撤销和添加新权限：
        $admin->syncPermissions( $permission_ids );

        return $this->ok();
    }
}
