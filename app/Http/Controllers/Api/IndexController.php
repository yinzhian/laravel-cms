<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Navigation;
use App\Models\Theme;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index ( Request $request )
    {
        return $this->ok( env("APP_ENV") );
    }

    /**
     * Notes: 获取快捷导航
     * User: 一颗地梨子
     * DateTime: 2022/3/5 16:44
     * @return \Illuminate\Http\JsonResponse
     */
    public function ad ( Request $request )
    {
        // 参数
        $position = $request->get( "position", NULL );
        $type     = $request->get( "type", NULL );

        return $this->ok( Ad::getAll( $position, $type ) );
    }

    /**
     * Notes: 获取快捷导航
     * User: 一颗地梨子
     * DateTime: 2022/3/5 16:44
     * @return \Illuminate\Http\JsonResponse
     */
    public function nav ()
    {

        return $this->ok( Navigation::getAll() );
    }

    /**
     * Notes: 主题
     * User: 一颗地梨子
     * DateTime: 2022/3/5 16:44
     * @return \Illuminate\Http\JsonResponse
     */
    public function theme ()
    {
        return $this->ok( Theme::getAll() );
    }
}
