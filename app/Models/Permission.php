<?php

namespace App\Models;

use App\Enums\IsMenuEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\SoftDeletes;

//class Permission extends Model
class Permission extends SpatiePermission
{
    use HasFactory;

    use SoftDeletes;

    protected $hidden = [ 'guard_name' ];

    protected function serializeDate ( \DateTimeInterface $date ) : string
    {
        return $date->format ( "Y-m-d H:i:s" );
    }

    //protected $appends = [ 'status_zh', 'is_menu_zh' ];

    public function getStatusZhAttribute () : string
    {
        return StatusEnum::getTitle ( $this->status );
    }

    public function getIsMenuZhAttribute () : string
    {
        return IsMenuEnum::getTitle ( $this->is_menu );
    }

    /**
     * Notes: 获取全部
     * User: 一颗地梨子
     * DateTime: 2022/2/18 15:30
     * @param int $parent_id
     * @return mixed
     */
    static function getAll ( int $parent_id = 0 )
    {
        return self::where ( "parent_id", $parent_id )
                   ->where ( "status", StatusEnum::ENABLE["key"] )
                   ->select ( "id", "parent_id", "name", "title" )
                   ->orderBy ( "sort", "DESC" )
                   ->orderBy ( "id", "DESC" )
                   ->get ();
    }

    /**
     * Notes: 列表
     * User: 一颗地梨子
     * DateTime: 2022/4/1 14:16
     * @param Request $request
     * @return mixed
     */
    static function list ( Request $request )
    {

        $permissions = self::when ( $request->name, function ( $query ) use ( $request ) {
            $query->where ( function ( $query ) use ( $request ) {
                $query->where ( "name", "LIKE", "%{$request->name}%" )
                      ->orWhere ( function ( $query ) use ( $request ) {
                          $query->where ( "title", "LIKE", "%{$request->name}%" )
                                ->orWhere ( function ( $query ) use ( $request ) {
                                    $query->where ( "route", "LIKE", "%{$request->name}%" );
                                } );
                      } );
            } );
        } )->when ( $request->is_menu, function ( $query ) use ( $request ) {
            $query->where ( "is_menu", $request->is_menu );
        } )->when ( filled ( $request->status ), function ( $query ) use ( $request ) {
            $query->where ( "status", $request->status );
        } )->when ( $request->parent_id, function ( $query ) use ( $request ) {
            $query->where ( "parent_id", $request->parent_id );
        } )->when ( $request->boolean ( "deleted" ), function ( $query ) {
            $query->onlyTrashed (); // 仅查询已删除的
        } )->select ( "id", "parent_id", "name", "title", "route", "icon", "is_menu", "status", "sort", 'updated_at', 'deleted_at' )
                           ->orderBy ( "sort", "DESC" )
                           ->orderBy ( "id", "DESC" )
                           ->paginate ( env ( "APP_PAGE", 20 ) );

        // 临时字段
        $permissions->data = $permissions->append ( [ 'status_zh', 'is_menu_zh' ] );

        return $permissions;

    }

    /**
     * Notes: 详情
     * User: 一颗地梨子
     * DateTime: 2022/2/18 13:43
     * @param $id
     * @return mixed
     */
    static function detail ( $id )
    {
        $permission = self::where ( "id", $id )
                          ->select ( "id", "parent_id", "name", "title", "route", "icon", "is_menu", "status", "sort", 'updated_at', 'deleted_at' )
                          ->first ();
        if ( $permission ) $permission->append ( [ "status_zh", "is_menu_zh" ] );
        return $permission;
    }
}
