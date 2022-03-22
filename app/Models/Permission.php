<?php

namespace App\Models;

use App\Enums\IsMenuEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\SoftDeletes;

//class Permission extends Model
class Permission extends SpatiePermission
{
    use HasFactory;

    use SoftDeletes;

    protected $hidden = ['guard_name'];

    protected function serializeDate(\DateTimeInterface $date): String
    {
        return $date->format( "Y-m-d H:i:s" );
    }

    //protected $appends = [ 'status_zh', 'is_menu_zh' ];

    public function getStatusZhAttribute () : string
    {
        return StatusEnum::getTitle($this->status);
    }

    public function getIsMenuZhAttribute () : string
    {
        return IsMenuEnum::getTitle($this->is_menu);
    }

    /**
     * Notes: 获取全部
     * User: 一颗地梨子
     * DateTime: 2022/2/18 15:30
     * @param int $parent_id
     * @return mixed
     */
    static function getAll ( int $parent_id = 0 ) {
        return self::where("parent_id", $parent_id)
                   ->where("status", StatusEnum::ENABLE["key"])
                   ->select("id", "parent_id", "name", "title")
                   ->orderBy("sort", "DESC")
                   ->orderBy("id", "DESC")
                   ->get();
    }

    /**
     * Notes: 列表
     * User: 一颗地梨子
     * DateTime: 2022/2/18 11:58
     * @param $parent_id
     * @param string $name
     * @param null $is_menu
     * @param null $status
     * @param bool $delete
     * @return mixed
     */
    static function list( $parent_id = NULL, string $name = "", $is_menu = NULL, $status = NULL, bool $delete = false ) {

        $permissions = self::when( $name, function ( $query ) use ( $name ) {
            $query->where( "name", "LIKE", "%{$name}%" )
                  ->orWhere( function ( $query ) use ( $name ) {
                      $query->where( "title", "LIKE", "%{$name}%" )
                            ->orWhere( function ( $query ) use ( $name ) {
                                $query->where( "route", "LIKE", "%{$name}%" );
                            });
                  });
        })->when( filled( $is_menu ), function ( $query ) use ( $is_menu ) {
            $query->where( "is_menu", $is_menu );
        })->when( filled( $status ), function ( $query ) use ( $status ) {
            $query->where( "status", $status );
        })->when( filled( $parent_id ), function ( $query ) use ( $parent_id ) {
            $query->where( "parent_id", $parent_id );
        })->when( $delete, function ( $query ) {
            $query->onlyTrashed(); // 仅查询已删除的
        })->select("id", "parent_id", "name", "title", "route", "icon", "is_menu", "status", "sort",'updated_at', 'deleted_at')
          ->orderBy("sort", "DESC")
          ->orderBy("id", "DESC")
          //->withTrashed() // 显示所有的，包括已经进行了软删除的
          ->paginate( env( "APP_PAGE", 20 ) );

        // 临时字段
        $permissions->data = $permissions->append(['status_zh', 'is_menu_zh']);

        return $permissions;

    }

    /**
     * Notes: 详情
     * User: 一颗地梨子
     * DateTime: 2022/2/18 13:43
     * @param $id
     * @return mixed
     */
    static function detail( $id ) {
        $permission = self::where( "id", $id )
                   ->select( "id", "parent_id", "name", "title", "route", "icon", "is_menu", "status", "sort", 'updated_at', 'deleted_at' )
                   ->first();
        if ($permission) $permission->append(["status_zh", "is_menu_zh"]);
        return $permission;
    }
}
