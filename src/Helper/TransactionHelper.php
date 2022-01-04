<?php


namespace Nichozuo\LaravelHelpers\Helper;


use Closure;
use Exception;
use Illuminate\Support\Facades\DB;

class TransactionHelper
{
    /**
     * @intro 开启事务
     * @param Closure $closure
     * @throws Exception
     */
    public static function Trans(Closure $closure)
    {
        try {
            DB::beginTransaction();
            $closure();
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
