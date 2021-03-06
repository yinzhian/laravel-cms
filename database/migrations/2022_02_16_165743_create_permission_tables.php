<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames  = config( 'permission.table_names' );
        $columnNames = config( 'permission.column_names' );
        $teams       = config( 'permission.teams' );

        if ( empty( $tableNames ) ) {
            throw new \Exception( 'Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.' );
        }
        if ( $teams && empty( $columnNames['team_foreign_key'] ?? null ) ) {
            throw new \Exception( 'Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.' );
        }

        Schema::create( $tableNames['permissions'], function ( Blueprint $table ) {
            $table->unsignedBigInteger("parent_id", false)->default(0)->comment("上级ID");
            $table->bigIncrements( 'id' );
            $table->string( 'name', 125 )->comment("中文名称 - 英文");
            $table->string( 'title', 125 )->nullable()->comment("权限名称");
            $table->string( 'route', 125 )->nullable()->comment("路由");
            $table->string( 'guard_name', 125 )->default("admin")->comment("登陆角色");
            $table->string( 'icon', 255 )->nullable()->comment("图标");
            $table->unsignedTinyInteger("is_menu", false)->default(\App\Enums\IsMenuEnum::MENU["key"])->comment("是否为菜单");
            $table->unsignedTinyInteger("status", false)->default(\App\Enums\StatusEnum::ENABLE["key"])->comment("状态");
            $table->unsignedTinyInteger("sort", false)->default(255)->comment("排序");
            $table->timestamps();
            $table->softDeletes();

            $table->unique( [ 'name', 'guard_name' ] );
        } );

        Schema::create( $tableNames['roles'], function ( Blueprint $table ) use ( $teams, $columnNames ) {
            $table->bigIncrements( 'id' );
            if ( $teams || config( 'permission.testing' ) ) { // permission.testing is a fix for sqlite testing
                $table->unsignedBigInteger( $columnNames['team_foreign_key'] )->nullable();
                $table->index( $columnNames['team_foreign_key'], 'roles_team_foreign_key_index' );
            }
            $table->string( 'name', 125 )->comment("角色名称 - 英文");
            $table->string( 'title', 125 )->nullable()->comment("权限名称");
            $table->string( 'guard_name', 125 )->default("admin")->comment("登陆角色");
            $table->unsignedTinyInteger("status", false)->default(\App\Enums\StatusEnum::ENABLE["key"])->comment("状态");
            $table->unsignedTinyInteger("sort", false)->default(255)->comment("排序");
            $table->timestamps();
            $table->softDeletes();
            if ( $teams || config( 'permission.testing' ) ) {
                $table->unique( [ $columnNames['team_foreign_key'], 'name', 'guard_name' ] );
            } else {
                $table->unique( [ 'name', 'guard_name' ] );
            }
        } );

        Schema::create( $tableNames['model_has_permissions'], function ( Blueprint $table ) use ( $tableNames, $columnNames, $teams ) {
            $table->unsignedBigInteger( PermissionRegistrar::$pivotPermission );

            $table->string( 'model_type' );
            $table->unsignedBigInteger( $columnNames['model_morph_key'] );
            $table->index( [ $columnNames['model_morph_key'], 'model_type' ], 'model_has_permissions_model_id_model_type_index' );

            $table->foreign( PermissionRegistrar::$pivotPermission )
                  ->references( 'id' )
                  ->on( $tableNames['permissions'] )
                  ->onDelete( 'cascade' );
            if ( $teams ) {
                $table->unsignedBigInteger( $columnNames['team_foreign_key'] );
                $table->index( $columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index' );

                $table->primary( [ $columnNames['team_foreign_key'], PermissionRegistrar::$pivotPermission, $columnNames['model_morph_key'], 'model_type' ],
                                 'model_has_permissions_permission_model_type_primary' );
            } else {
                $table->primary( [ PermissionRegistrar::$pivotPermission, $columnNames['model_morph_key'], 'model_type' ],
                                 'model_has_permissions_permission_model_type_primary' );
            }

        } );

        Schema::create( $tableNames['model_has_roles'], function ( Blueprint $table ) use ( $tableNames, $columnNames, $teams ) {
            $table->unsignedBigInteger( PermissionRegistrar::$pivotRole );

            $table->string( 'model_type' );
            $table->unsignedBigInteger( $columnNames['model_morph_key'] );
            $table->index( [ $columnNames['model_morph_key'], 'model_type' ], 'model_has_roles_model_id_model_type_index' );

            $table->foreign( PermissionRegistrar::$pivotRole )
                  ->references( 'id' )
                  ->on( $tableNames['roles'] )
                  ->onDelete( 'cascade' );
            if ( $teams ) {
                $table->unsignedBigInteger( $columnNames['team_foreign_key'] );
                $table->index( $columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index' );

                $table->primary( [ $columnNames['team_foreign_key'], PermissionRegistrar::$pivotRole, $columnNames['model_morph_key'], 'model_type' ],
                                 'model_has_roles_role_model_type_primary' );
            } else {
                $table->primary( [ PermissionRegistrar::$pivotRole, $columnNames['model_morph_key'], 'model_type' ],
                                 'model_has_roles_role_model_type_primary' );
            }
        } );

        Schema::create( $tableNames['role_has_permissions'], function ( Blueprint $table ) use ( $tableNames ) {
            $table->unsignedBigInteger( PermissionRegistrar::$pivotPermission );
            $table->unsignedBigInteger( PermissionRegistrar::$pivotRole );

            $table->foreign( PermissionRegistrar::$pivotPermission )
                  ->references( 'id' )
                  ->on( $tableNames['permissions'] )
                  ->onDelete( 'cascade' );

            $table->foreign( PermissionRegistrar::$pivotRole )
                  ->references( 'id' )
                  ->on( $tableNames['roles'] )
                  ->onDelete( 'cascade' );

            $table->primary( [ PermissionRegistrar::$pivotPermission, PermissionRegistrar::$pivotRole ], 'role_has_permissions_permission_id_role_id_primary' );
        } );

        app( 'cache' )
            ->store( config( 'permission.cache.store' ) != 'default' ? config( 'permission.cache.store' ) : null )
            ->forget( config( 'permission.cache.key' ) );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config( 'permission.table_names' );

        if ( empty( $tableNames ) ) {
            throw new \Exception( 'Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.' );
        }

        Schema::drop( $tableNames['role_has_permissions'] );
        Schema::drop( $tableNames['model_has_roles'] );
        Schema::drop( $tableNames['model_has_permissions'] );
        Schema::drop( $tableNames['roles'] );
        Schema::drop( $tableNames['permissions'] );
    }
}
