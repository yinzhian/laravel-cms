<?php

namespace App\Models;

use App\Enums\LabelTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Label extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected function serializeDate( \DateTimeInterface $date ) : string
    {
        return $date->format( "Y-m-d H:i:s" );
    }

    public function getTypeZhAttribute() : string
    {
        return LabelTypeEnum::getTitle( $this->type );
    }

    /**
     * Notes: 全部
     * User: 一颗地梨子
     * DateTime: 2022/3/23 17:55
     * @param int|mixed $type
     * @return mixed
     */
    static function getAll( int $type = LabelTypeEnum::ARTICLE["key"] ) {
        return self::where("type", $type)
                   ->select( "id", "title" )
                   ->orderBy( "sort", "DESC" )
                   ->orderBy( "id", "DESC" )
                   ->get();
    }

    /**
     * Notes: 列表
     * User: 一颗地梨子
     * DateTime: 2022/3/23 17:54
     * @param string $title
     * @param null $type
     * @param bool $deleted
     * @return mixed
     */
    static function list( String $title = "", $type = NULL, bool $deleted = false ) {

        $labels = self::when( $title, function ( $query ) use ( $title ) {
            $query->where( "title", "LIKE", "%{$title}%" );
        } )->when( filled( $type ), function ( $query ) use ( $type ) {
            $query->where( "type", $type );
        } )->when( $deleted, function ( $query ) {
            $query->onlyTrashed(); // 仅查询已删除的
        })->select( "id", "title", "color", "icon", "type", "sort", "created_at", 'updated_at', 'deleted_at' )
          ->orderBy( "sort", "DESC" )
          ->paginate( env( "APP_PAGE", 20 ) );

        // 临时字段
        $labels->data = $labels->append( [ 'type_zh' ] );

        return $labels;
    }

    /**
     * Notes: 详情
     * User: 一颗地梨子
     * DateTime: 2022/3/23 17:54
     * @param int $id
     * @return mixed
     */
    static function detail ( int $id ) {
        $label = self::where("id", $id)
                  ->select( "id", "title", "color", "icon", "type", "sort", "created_at", 'updated_at', 'deleted_at' )
                  ->first();

        if ($label) $label->append( [ 'type_zh' ] );

        return $label;
    }
}
