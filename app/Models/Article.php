<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Article extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected function serializeDate( \DateTimeInterface $date ) : string
    {
        return $date->format( "Y-m-d H:i:s" );
    }

    /**
     * Notes: 文章类目
     * User: 一颗地梨子
     * DateTime: 2022/3/23 18:29
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function article_category()
    {
        return $this->belongsTo( ArticleCategory::class )->select( "id", "title" );
    }

    /**
     * Notes: 文章标签
     * User: 一颗地梨子
     * DateTime: 2022/3/24 10:05
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function label()
    {
        return $this->belongsToMany( Label::class, "article_labels", "article_id", "label_id" );
    }

    /**
     * Notes: 列表
     * User: 一颗地梨子
     * DateTime: 2022/4/1 13:53
     * @param Request $request
     * @return mixed
     */
    static function list( Request $request )
    {

        return self::when( $request->filled( "title" ), function ( $query ) use ( $request ) {
            $query->where( "title", "LIKE", "%{$request->title}%" );
        } )->when( $request->filled( "article_category_id" ), function ( $query ) use ( $request ) {
            $query->where( "article_category_id", $request->article_category_id );
        } )->when( $request->filled( "deleted" ), function ( $query ) {
            $query->onlyTrashed(); // 仅查询已删除的
        } )->select( "id", "article_category_id", "title", "cover", "read_num", "sort", "created_at", 'updated_at', 'deleted_at' )
                   ->with( [ "article_category" ] )
                   ->orderBy( "sort", "DESC" )
                   ->orderBy( "id", "DESC" )
                   ->paginate( env( "APP_PAGE", 20 ) );

    }

    /**
     * Notes: 详情
     * User: 一颗地梨子
     * DateTime: 2022/3/14 17:53
     * @param int $id
     * @return mixed
     */
    static function detail( int $id )
    {
        return self::where( "id", $id )
                   ->select( "id", "article_category_id", "title", "cover", "describe", "content", "read_num", "sort", "created_at", 'updated_at', 'deleted_at' )
                   ->with( [ "article_category", "label" ] )
                   ->first();
    }

    /**
     * Notes: 发布文章
     * User: 一颗地梨子
     * DateTime: 2022/3/24 10:19
     * @param array $params
     * @return bool
     * @throws \Exception
     */
    static function publish( array $params ) : bool
    {

        DB::beginTransaction();

        try {

            // 文章入库
            $label = $params["label"] ?? [];
            unset( $params["label"] );
            $params["admin_id"] = Admin::getAdminId();
            $id                 = self::insertGetId( $params );
            if ( $label && $id ) {
                // 处理标签
                ArticleLabel::addLabel( $id, $label );
            }

            DB::commit();

        } catch ( \Exception $exception ) {
            DB::rollBack();
            throw new \Exception( "服务异常" );
        }

        return true;
    }

    /**
     * Notes: 更新发布
     * User: 一颗地梨子
     * DateTime: 2022/3/24 10:22
     * @param array $params
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    static function updatePublish( array $params, int $id ) : bool
    {

        DB::beginTransaction();

        try {

            // 文章入库
            $label = $params["label"] ?? [];
            unset( $params["label"] );
            self::where( "id", $id )->update( $params );
            // 处理标签
            ArticleLabel::addLabel( $id, $label );

            DB::commit();

        } catch ( \Exception $exception ) {
            DB::rollBack();
            throw new \Exception( "服务异常" );
        }

        return true;
    }
}
