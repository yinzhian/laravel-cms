<?php

namespace App\Models;

use App\Enums\ClientEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperateLog extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $hidden = [ 'updated_at', 'deleted_at' ];

    protected function serializeDate( \DateTimeInterface $date ) : string
    {
        return $date->format( "Y-m-d H:i:s" );
    }

    /**
     * Notes: 终端
     * User: 一颗地梨子
     * DateTime: 2022/2/19 16:28
     * @return string
     */
    public function getClientTypeZhAttribute() : string
    {
        return ClientEnum::getTitle( $this->client_type );
    }

    /**
     * Notes: 访问用户
     * User: 一颗地梨子
     * DateTime: 2022/2/19 16:28
     * @return string
     */
    public function getMemberZhAttribute() : string
    {
        if ( ClientEnum::MEMBER["key"] == $this->client_type ) {
            return (string) Member::where( "id", $this->m_id )->value( "phone" );
        } elseif ( ClientEnum::ADMIN["key"] == $this->client_type ) {
            return (string) Admin::where( "id", $this->m_id )->value( "username" );
        }
    }

    /**
     * Notes: 列表
     * User: 一颗地梨子
     * DateTime: 2022/2/19 16:03
     * @param string $route
     * @param string $member
     * @param string $method
     * @param string $client_type
     * @return mixed
     */
    static function list( string $route = "", string $member = "", string $method = "", string $client_type = "" )
    {
        $logs = self::when( $route, function ( $query ) use ( $route ) {
            $query->where( "route", "LIKE", "%{$route}%" )
                  ->orWhere( function ( $query ) use ( $route ) {
                      $query->where( "path", "LIKE", "%{$route}%" );
                  } );
        } )->when( $client_type, function ( $query ) use ( $client_type ) {
            $query->where( "client_type", $client_type );
        } )->when( $member, function ( $query ) use ( $member ) {
            $m_id
                = $query->where( "m_id", $member );
        } )->when( $method, function ( $query ) use ( $method ) {
            $query->where( "method", $method );
        } )->withTrashed() // 显示所有的，包括已经进行了软删除的
                    ->orderBy( "id", "DESC" )
                    ->paginate( env( "APP_PAGE", 20 ) );

        // 临时字段
        $logs->data = $logs->append( [ 'client_type_zh', "member_zh" ] );

        return $logs;
    }

    /**
     * Notes: 详情
     * User: 一颗地梨子
     * DateTime: 2022/2/19 16:05
     * @param $id
     * @return mixed
     */
    static function detail( $id )
    {
        return self::where( "id", $id )
                   ->first()
                   ->append( [ "client_type_zh", "member_zh" ] );
    }
}
