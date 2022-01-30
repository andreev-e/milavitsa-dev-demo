<?php
namespace App\Services;

class BaseService
{
    /**
     * @param mixed ...$params
     * @return BaseService
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }
}
