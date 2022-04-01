<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
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
        // 参数
        $article_category_id = (int) $request->get( "article_category_id", 0 );
        $title               = (string) $request->get( "title", "" );
        $deleted             = (bool) $request->get( "deleted", false );

        return $this->ok( Article::list( $article_category_id, $title, $deleted ) );
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
        return $this->ok( Article::detail( (int) $id ) );
    }

    /**
     * Notes: 添加
     * User: 一颗地梨子
     * DateTime: 2022/3/14 18:03
     * @param ArticleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create( ArticleRequest $request )
    {
        if ( ! Article::publish( $request->all() ) ) {
            return $this->fail();
        }

        return $this->ok();
    }

    /**
     * Notes: 更新
     * User: 一颗地梨子
     * DateTime: 2022/3/14 18:04
     * @param ArticleRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update( ArticleRequest $request, $id )
    {
        if ( ! Article::updatePublish ( $request->all(), $id ) ) {
            return $this->fail();
        }
        return $this->ok();
    }

    /**
     * Notes: 删除
     * User: 一颗地梨子
     * DateTime: 2022/3/14 18:04
     * @param ArticleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete( ArticleRequest $request )
    {
        if ( ! Article::whereIn( "id", $request->get( "ids" ) )->delete() ) {

            return $this->fail();
        }
        return $this->ok();
    }

    /**
     * Notes: 还原
     * User: 一颗地梨子
     * DateTime: 2022/3/22 11:29
     * @param ArticleRequest $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function restore( ArticleRequest $request )
    {
        if ( ! Article::whereIn( "id", $request->get( "ids" ) )->restore() ) {
            return $this->fail();
        }
        return $this->ok();
    }
}
