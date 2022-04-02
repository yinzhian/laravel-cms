<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleCategoryRequest;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class ArticleCategoryController extends Controller
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
        return $this->ok( ArticleCategory::list( $request ) );
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
        return $this->ok( ArticleCategory::detail( (int) $id ) );
    }

    /**
     * Notes: 添加
     * User: 一颗地梨子
     * DateTime: 2022/3/14 18:03
     * @param ArticleCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create( ArticleCategoryRequest $request )
    {
        if ( ! ArticleCategory::insert( $request->all() ) ) {
            return $this->fail();
        }

        return $this->ok();
    }

    /**
     * Notes: 更新
     * User: 一颗地梨子
     * DateTime: 2022/3/14 18:04
     * @param ArticleCategoryRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update( ArticleCategoryRequest $request, $id )
    {
        if ( ! ArticleCategory::where( "id", $id )->update( $request->all() ) ) {
            return $this->fail();
        }
        return $this->ok();
    }

    /**
     * Notes: 删除
     * User: 一颗地梨子
     * DateTime: 2022/3/14 18:04
     * @param ArticleCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete( ArticleCategoryRequest $request )
    {
        if ( ! ArticleCategory::whereIn( "id", $request->get( "ids" ) )->delete() ) {

            return $this->fail();
        }
        return $this->ok();
    }

    /**
     * Notes: 还原
     * User: 一颗地梨子
     * DateTime: 2022/3/22 11:29
     * @param ArticleCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function restore( ArticleCategoryRequest $request )
    {
        if ( ! ArticleCategory::whereIn( "id", $request->get( "ids" ) )->restore() ) {
            return $this->fail();
        }
        return $this->ok();
    }
}
