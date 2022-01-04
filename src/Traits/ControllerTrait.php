<?php


namespace Nichozuo\LaravelHelpers\Traits;


use Nichozuo\LaravelHelpers\Exceptions\Err;

trait ControllerTrait
{
    /**
     * @intro 获得分页size
     * @return int
     * @throws Err
     */
    protected function perPage(): int
    {
        $params = request()->only('perPage');
        if (!isset($params['perPage']) || is_numeric($params['perPage']))
            return 20;

        $allow = config('nichozuo.perPageAllow', [10, 20, 50, 100]);
        if (!in_array($params['perPage'], $allow))
            throw Err::NewText('分页数据不在规定范围内');

        return (int)$params['perPage'];
    }

    /**
     * @intro 获得mines
     * @return string
     */
    protected function getMines(): string
    {
        $mime_image = 'gif,jpeg,png,ico,svg';
        $mine_docs = 'xls,xlsx,doc,docx,ppt,pptx,pdf';
        $mine_zip = '7z,zip,rar';
        return $mime_image . ',' . $mine_docs . ',' . $mine_zip;
    }
}
