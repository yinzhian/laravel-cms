<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfigRequest;
use App\Models\Config;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
     * Notes: 列表
     * User: 一颗地梨子
     * DateTime: 2022/2/19 16:57
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index ( Request $request )
    {
        // 参数
        $parent_id = $request->get( "parent_id", 0 );

        return $this->ok(Config::list( $parent_id ));
    }

    /**
     * Notes: 详情
     * User: 一颗地梨子
     * DateTime: 2022/2/19 16:59
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail ( $id )
    {
        return $this->ok(Config::detail( $id ));
    }

    /**
     * Notes: 添加
     * User: 一颗地梨子
     * DateTime: 2022/2/19 17:08
     * @param ConfigRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create ( ConfigRequest $request )
    {
        Config::insert( $request->all() );
        return $this->ok();
    }

    /**
     * Notes: 修改
     * User: 一颗地梨子
     * DateTime: 2022/2/19 17:09
     * @param ConfigRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update ( ConfigRequest $request, $id )
    {
        Config::where("id", $id)->update( $request->all() );
        return $this->ok();
    }
}
