<?php


namespace app\model;


use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * 不可批量赋值的属性。
     * @var array
     */
    protected $guarded = [];


    //时间
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
