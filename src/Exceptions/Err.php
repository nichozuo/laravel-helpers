<?php


namespace Nichozuo\LaravelHelpers\Exceptions;


class Err extends BaseException
{
    const AuthUserNotLogin = [10000, '认证失败', '用户未登陆'];

    /**
     * @intro 抛出具有格式的新错误
     * @param array $arr
     * @param string $description
     * @return static
     */
    public static function New(array $arr, string $description = ''): Err
    {
        if ($description == '' && count($arr) == 3)
            $description = $arr[2];
        return new static((int)$arr[0], $arr[1], $description);
    }

    /**
     * @intro 抛出新错误
     * @param $description
     * @return static
     */
    public static function NewText($description): Err
    {
        return new static(999, '发生错误', $description);
    }
}
