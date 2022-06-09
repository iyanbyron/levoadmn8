<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('SQL_DEBUG'))//true开启  false不输出sql日志
        {
            \DB::listen(
                function ($sql) {
                    foreach ($sql->bindings as $i => $binding) {
                        if ($binding instanceof \DateTime) {
                            $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                        } else {
                            if (is_string($binding)) {
                                $sql->bindings[$i] = "'$binding'";
                            }
                        }
                    }
                    // Insert bindings into query
                    $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);
                    $query = vsprintf($query, $sql->bindings);
                    // Save the query to file
                    $logFile = storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_query.log');
                    file_put_contents($logFile, date('Y-m-d H:i:s') . ': ' . $query . PHP_EOL, FILE_APPEND);
                }
            );
        }
        //左侧菜单
        view()->composer('admin.layout', function ($view) {
            $menus = \App\Models\Permission::with(['childs'])->where('parent_id', 0)->orderBy('sort', 'desc')->get();
            $view->with('menus', $menus);
            $unreadMessage = [];
            $view->with('unreadMessage', $unreadMessage);
        });
    }
}
