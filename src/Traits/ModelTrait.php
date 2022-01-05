<?php


namespace Nichozuo\LaravelHelpers\Traits;


use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Nichozuo\LaravelHelpers\Exceptions\Err;

trait ModelTrait
{
    /**
     * @intro 如果key/value存在，添加where条件
     * @param Builder $query
     * @param array $params
     * @param string $key
     * @param string $field
     * @return Builder
     */
    public function scopeIfWhere(Builder $query, array $params, string $key, string $field = ''): Builder
    {
        if (isset($params[$key])) {
            $field = ($field == '') ? $key : $field;
            return $query->where($field, $params[$key]);
        }
        return $query;
    }

    /**
     * @intro 如果key/value存在，添加whereIn条件
     * @param Builder $query
     * @param array $params
     * @param string $key
     * @param string $field
     * @return Builder
     * @throws Err
     */
    public function scopeIfWhereIn(Builder $query, array $params, string $key, string $field = ''): Builder
    {
        if (isset($params[$key])) {
            if (is_array($params[$key]) && count($params[$key]) == 0)
                throw Err::NewText('ifWhereIn的参数必须是数组');
            $field = ($field == '') ? $key : $field;
            return $query->whereIn($field, $params[$key]);
        }
        return $query;
    }

    /**
     * @intro 如果key/value存在，添加whereLike条件
     * @param Builder $query
     * @param array $params
     * @param string $key
     * @param string $field
     * @return mixed
     */
    public function scopeIfWhereLike(Builder $query, array $params, string $key, string $field = ''): Builder
    {
        if (isset($params[$key])) {
            $field = ($field == '') ? $key : $field;
            return $query->where($field, 'like', "%{$params[$key]}%");
        }
        return $query;
    }

    /**
     * @intro 如果key/value存在，添加whereBetween条件
     * @param Builder $query
     * @param array $params
     * @param string $key
     * @param string $field
     * @param string $type
     * @param string $op1
     * @param string $op2
     * @return Builder
     * @throws Err
     */
    public function scopeIfRange(Builder $query, array $params, string $key, string $field = '', string $type = 'float', string $op1 = '<', string $op2 = '>='): Builder
    {
        if (isset($params[$key])) {
            $field = ($field == '') ? $key : $field;
            $a = $params[$key];

            if (is_array($a) && count($a) != 2)
                throw Err::NewText('ifRange参数必须是数组，且有2位');

            // 数据类型
            if ($type == 'date') {
                $a[0] = $a[0] == "" ? "" : Carbon::parse($a[0])->toDateString();
                $a[1] = $a[1] == "" ? "" : Carbon::parse($a[1])->toDateString();
            } elseif ($type == 'datetime') {
                $a[0] = $a[0] == "" ? "" : Carbon::parse($a[0])->toDateTimeString();
                $a[1] = $a[1] == "" ? "" : Carbon::parse($a[1])->toDateTimeString();
            } else {
                $a[0] = $a[0] == "" ? "" : floatval($a[0]);
                $a[1] = $a[1] == "" ? "" : floatval($a[1]);
            }

            // 判断逻辑
            if ($a[0] == "" && $a[1] == "")
                return $query;
            else if ($a[0] == "")
                return $query->where($field, $op1, $a[1]);
            else if ($a[1] == "")
                return $query->where($field, $op2, $a[0]);
            else
                return $query->whereBetween($field, $a);
        }
        return $query;
    }

    /**
     * @intro 排序，默认id倒序
     * @param Builder $query
     * @param string $key
     * @return Builder
     */
    public function scopeOrder(Builder $query, string $key = 'orderBy'): Builder
    {
        $params = request()->only($key);
        if (isset($params[$key])) {
            $orderBy = $params[$key];
            if (count($orderBy) == 2) {
                if ($orderBy[1] == 'descend') {
                    return $query->orderBy($orderBy[0], 'desc');
                } elseif ($orderBy[1] == 'ascend') {
                    return $query->orderBy($orderBy[0], 'asc');
                }
            }
        }
        return $query->orderByDesc('id');
    }

    /**
     * @param $query
     * @param $params
     * @param $keys
     * @param null $label
     * @return mixed
     * @throws Err
     */
    public function scopeUnique($query, $params, $keys, $label = null)
    {
        $data = Arr::only($params, $keys);
        $model = $query->where($data)->first();
        if ($model && $label != null) {
            if (!isset($params['id']) || $model->id != $params['id'])
                throw Err::NewText("{$label}【{$params[$keys[0]]}】已存在，请重试");
        }
        return $query;
    }

    /**
     * @param $query
     * @param string $key
     * @return mixed
     */
    public function scopeNewest($query, string $key = 'updated_at')
    {
        return $query->orderBy($key, 'desc');
    }

    /**
     * @param $keys
     * @param $params
     * @param null $errMessage
     * @return bool
     * @throws Err
     */
    public static function CheckUnique($keys, $params, $errMessage = null): bool
    {
        $where = Arr::only($params, $keys);
        $model = self::where($where)->first();
        if (!$model) {
            return true;
        } else {
            if ($errMessage != null)
                throw Err::NewText($errMessage);
            return false;
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws Err
     */
    public static function findOrError($id)
    {
        $model = self::find($id);
        if (!$model)
            throw Err::NewText("没有此【" . self::$name . "】记录");
        return $model;
    }

    /**
     * @intro 统一处理返回时间的格式
     * @param DateTimeInterface $dateTime
     * @return string
     */
    public function serializeDate(DateTimeInterface $dateTime): string
    {
        return $dateTime->format('Y-m-d H:i:s');
    }
}
