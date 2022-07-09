<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class ArticleCategory extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected function serializeDate( \DateTimeInterface $date ) : string
    {
        return $date->format( "Y-m-d H:i:s" );
    }

    /**
     * Notes: 列表
     * User: 一颗地梨子
     * DateTime: 2022/4/1 13:52
     * @param Request $request
     * @return mixed
     */
    static function list( Request $request )
    {
        return self::when( $request->title, function ( $query ) use ( $request ) {
            $query->where( "title", "LIKE", "%{$request->title}%" );
        } )->when( $request->boolean( "deleted" ), function ( $query ) {
            $query->onlyTrashed(); // 仅查询已删除的
        } )->select( "id", "title", "sort", "created_at", 'updated_at', 'deleted_at' )
                   ->orderBy( "sort", "DESC" )
                   ->orderBy( "id", "DESC" )
                   ->get();
    }

    /**
     * Notes: 详情
     * User: 一颗地梨子
     * DateTime: 2022/3/23 17:54
     * @param int $id
     * @return mixed
     */
    static function detail( int $id )
    {
        return self::where( "id", $id )
                   ->select( "id", "title", "sort", "created_at", 'updated_at', 'deleted_at' )
                   ->first();
    }
}
