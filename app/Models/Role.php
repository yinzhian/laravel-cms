<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\SoftDeletes;

//class Role extends Model
class Role extends SpatieRole
{

    use HasFactory;

    use SoftDeletes;

    protected $guard_name = 'admin'; // or whatever guard you want to use

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

    /**
     * Notes: 获取全部
     * User: 一颗地梨子
     * DateTime: 2022/2/18 15:30
     * @param int $parent_id
     * @return mixed
     */
    static function getAll ( ) {
        return self::where("status", StatusEnum::ENABLE["key"])
                   ->select("id", "name", "title")
                   ->orderBy("sort", "DESC")
                   ->orderBy("id", "DESC")
                   ->get();
    }

    /**
     * Notes: 角色
     * User: 一颗地梨子
     * DateTime: 2022/2/18 11:11
     * @param string $name
     * @param null $status
     * @param bool $deleted
     * @return mixed
     */
    static function list( string $name = "", $status = NULL, bool $deleted = false ) {

        $roles = self::when( $name, function ( $query ) use ( $name ) {
            $query->where( "name", "LIKE", "%{$name}%" )
                  ->orWhere( function ( $query ) use ( $name ) {
                      $query->where( "title", "LIKE", "%{$name}%" );
                  });
        })->when( filled( $status ), function ( $query ) use ( $status ) {
            $query->where( "status", $status );
        })->when( $deleted, function ( $query ) {
            $query->onlyTrashed(); // 仅查询已删除的
        })->select("id", "name", "status", "sort", "created_at",'updated_at', 'deleted_at')
          ->orderBy("sort", "DESC")
          ->orderBy("id", "DESC")
          //->withTrashed() // 显示所有的，包括已经进行了软删除的
          ->paginate( env( "APP_PAGE", 20 ) );

        // 临时字段
        $roles->data = $roles->append(['status_zh']);

        return $roles;
    }

    /**
     * Notes: 详情
     * User: 一颗地梨子
     * DateTime: 2022/2/18 11:13
     * @param $id
     * @return mixed
     */
    static function detail( $id ) {

        $role = self::where("id", $id)
                    ->select("id", "name", "status", "sort",  "created_at", 'updated_at', 'deleted_at')
                    ->first();

        $role->append(["status_zh"]);

        $role->getAllPermissions();

        return $role;
    }
}
