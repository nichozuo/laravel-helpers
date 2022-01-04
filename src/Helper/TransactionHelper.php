<?php


namespace Nichozuo\LaravelHelpers\Helper;


use Closure;
use Exception;
use Illuminate\Support\Facades\DB;

class TransactionHelper
{
    /**
     * @param Closure $closure
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
