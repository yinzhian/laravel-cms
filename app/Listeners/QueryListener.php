<?php

namespace App\Listeners;

use Illuminate\Database\Events\QueryExecuted;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class QueryListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\\Database\\Events\\QueryExecuted  $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {
        if (env('APP_ENV', 'production') == 'local') {
            $sql = str_replace("?", "'%s'", $event->sql);
            $log = vsprintf($sql, $event->bindings);

            // 自定义SQL位置
            ( new Logger( env( "APP_ENV", "local" ) ) )
                ->pushHandler( new RotatingFileHandler( storage_path( "logs/sql/sql.log" ) ) )
                ->info( $log );

            //Log::info($log);
        }
    }
}
