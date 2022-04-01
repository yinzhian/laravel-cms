<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleLabel extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected function serializeDate( \DateTimeInterface $date ) : string
    {
        return $date->format( "Y-m-d H:i:s" );
    }

    /**
     * Notes: 添加文章标签
     * User: 一颗地梨子
     * DateTime: 2022/3/24 10:18
     * @param int $article_id
     * @param array $label
     */
    static function addLabel( int $article_id, array $label )
    {

        self::where( "article_id", $article_id )->delete();

        if ( $label ) {
            foreach ( $label as $label_id ) {
                self::insert( [
                                  "article_id" => $article_id,
                                  "label_id"   => $label_id,
                              ] );
            }
        }

    }
}
