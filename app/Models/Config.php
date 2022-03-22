<?php

namespace App\Models;

use App\Enums\ConfigTypeEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Config extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $hidden = [];

    protected function serializeDate(\DateTimeInterface $date): String
    {
        return $date->format( "Y-m-d H:i:s" );
    }

    //protected $appends = [ 'status_zh'];

    public function getStatusZhAttribute () : string
    {
        return StatusEnum::getTitle($this->status);
    }

    public function getTypeZhAttribute () : string
    {
        return ConfigTypeEnum::getTitle($this->type);
    }

    /**
     * Notes: 获取配置列表
     * User: 一颗地梨子
     * DateTime: 2022/2/19 16:57
     * @param int $parent_id
     * @return mixed
     */
    static function list ( int $parent_id = 0 ) {

        $configs = self::where("parent_id", $parent_id)
            ->select( "id", "parent_id", "name", "key", "value", "type", "option", "sort", "status",
                      "created_at", 'updated_at', 'deleted_at' )
            ->orderBy( "sort", "DESC" )
            ->orderBy( "id", "DESC" )
            ->get();

        // 临时字段
        $configs->data = $configs->append(['status_zh', 'type_zh']);

        return $configs;
    }

    /**
     * Notes: 详情
     * User: 一颗地梨子
     * DateTime: 2022/2/19 16:58
     * @param $id
     * @return mixed
     */
    static function detail( $id ) {
        $config = self::where( "id", $id )
                   ->select( "id", "parent_id", "name", "key", "value", "type", "option", "sort", "status",
                             "created_at",'updated_at', 'deleted_at' )
                   ->first();

        if ( $config ) $config->append(["status_zh", "type_zh"]);

        return $config;
    }

    /**
     * Notes: 获取配置值
     * User: 一颗地梨子
     * DateTime: 2022/3/5 18:01
     * @param String $key
     * @param String $defaultValue
     * @return mixed
     */
    static function getValue ( String $key, $defaultValue = "" ) {
        return self::where( "key", $key )
                    ->where( "status", StatusEnum::ENABLE["key"] )
                   ->value("value") ?? env( $key, $defaultValue );
    }
}
