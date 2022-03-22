<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 添加超级管理员账号
        $admin = Admin::create( [
                                    "username"  => "admin",
                                    "password"  => Hash::make( "admin123" ),
                                    "real_name" => "管理员",
                                    "phone"     => "13033790000",
                                    "email"     => "yza8023@qq.com",
                                ] );

        // 添加超级管理员权限
        $role = Role::create( [
                                  "name"       => env( "APP_SUPER", "SuperAdmin" ),
                                  "title"      => "超级管理员",
                                  "guard_name" => "admin",
                              ] );

        // 给超级管理员赋予超级权限
        $admin->syncRoles( $role );

        echo "后台地址：uri/admin/login" . "\r\n";
        echo "账号：admin" . "\r\n";
        echo "密码：admin123" . "\r\n";
    }
}
