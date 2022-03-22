<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{

    use HasRoles;

    use HasFactory;

    use SoftDeletes;

    protected $guard_name = 'admin'; // or whatever guard you want to use

    protected $hidden = ['password'];

    protected function serializeDate(\DateTimeInterface $date): String
    {
        return $date->format( "Y-m-d H:i:s" );
    }

    //protected $appends = [ 'status_zh'];

    public function getStatusZhAttribute () : string
    {
        return StatusEnum::getTitle($this->status);
    }

    public function getJWTIdentifier()
    {
        // TODO: Implement getJWTIdentifier() method.
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        // TODO: Implement getJWTCustomClaims() method.
        return ['role' => 'admin'];
    }

    /**
     * Notes: 获取登陆管理员ID
     * User: 一颗地梨子
     * DateTime: 2022/2/15 16:40
     * @return int
     */
    static function getAdminId (): int {
        return (int) auth("admin")->id();
    }

    /**
     * Notes: 管理员详情
     * User: 一颗地梨子
     * DateTime: 2022/2/21 11:08
     * @param int $admin_id
     * @return mixed
     */
    static function getAdmin ( $admin_id = 0 ) {
        $admin_id = $admin_id ? $admin_id : self::getAdminId ();
        $admins = self::where("id", $admin_id)
                   ->select("id", "username", "real_name", "phone", "email", "avatar", "status", "created_at", 'updated_at', 'deleted_at')
                   ->first()
                   ->append(['status_zh']);

        $admins->getAllPermissions();

        return $admins;
    }

    /**
     * Notes: 管理员列表
     * User: 一颗地梨子
     * DateTime: 2022/2/17 11:39
     * @param string $username
     * @param null $status
     * @return mixed
     */
    static function list( String $username = "", $status = NULL, bool $deleted = false ) {

        $admins = self::when( $username, function ( $query ) use ( $username ) {
            $query->where( "username", "LIKE", "%{$username}%" )
                  ->orWhere( function ( $query ) use ( $username ) {
                      $query->where( "real_name", "LIKE", "%{$username}%" );
                  })
                  ->orWhere( function ( $query ) use ( $username ) {
                      $query->where( "phone", "LIKE", "%{$username}%" );
                  })
                  ->orWhere( function ( $query ) use ( $username ) {
                      $query->where( "email", "LIKE", "%{$username}%" );
                  });
        })->when( filled( $status ), function ( $query ) use ( $status ) {
            $query->where( "status", $status );
        })->when( $deleted, function ( $query ) {
            $query->onlyTrashed(); // 仅查询已删除的
        })->select("id", "username", "real_name", "phone", "email", "avatar", "status", "created_at", 'updated_at', 'deleted_at')
          // ->withTrashed() // 显示所有的，包括已经进行了软删除的
          ->paginate( env( "APP_PAGE", 20 ) );

        // 临时字段
        $admins->data = $admins->append(['status_zh']);

        return $admins;
    }

    /**
     * Notes: 详情
     * User: 一颗地梨子
     * DateTime: 2022/2/17 11:43
     * @param $id
     * @return mixed
     */
    static function detail( $id ) {

        $admin = self::where( "id", $id )
                   ->select( "id", "username", "real_name", "phone", "email", "avatar", "status", 'updated_at', 'deleted_at' )
                   ->first();

        if ($admin) $admin->append(["status_zh"]);

        $admin->getAllPermissions();

        return $admin;
    }
}
