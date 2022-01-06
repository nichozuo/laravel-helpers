<?php


namespace Nichozuo\LaravelHelpers\Helper;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SQLHelper
{
    /**
     * @intro 记录sql日志
     */
    public static function logSql()
    {
        if (config('app.debug')) {
            DB::listen(function ($query) {
                $tmp = str_replace('?', '"' . '%s' . '"', $query->sql);
                $qBindings = [];
                if (!empty($query->bindings)) {
                    foreach ($query->bindings as $key => $value) {
                        if (is_numeric($key)) {
                            $qBindings[] = $value;
                        } else {
                            $tmp = str_replace(':' . $key, '"' . $value . '"', $tmp);
                        }
                    }
                    $tmp = vsprintf($tmp, $qBindings);
                    $tmp = str_replace("\\", "", $tmp);
                    Log::info(' execution time: ' . $query->time . 'ms; ' . $tmp . "\n\n\t");
                }
            });
        }
    }

    /**
     * @intro Fix unique key is too long
     */
    public static function Schema()
    {
        Schema::defaultStringLength(191);
    }
}
