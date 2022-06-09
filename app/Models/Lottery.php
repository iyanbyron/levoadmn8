<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Lottery extends Model
{
    //  public $timestamps = false;
    /* const CREATED_AT = 'create_time';
     const UPDATED_AT = 'update_time';*/
    protected $table = 'lottery';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_id',
        'sort',
        'issue',
        'win_number',
        'is_open',
        'open_time',
        'created_at',
        'updated_at'
    ];

    /**
     * 格式化TZ日期格式
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');//年月日格式
        //return $date->format($this->dateFormat ?: 'U');//默认时间戳格式
    }
    /*
    * 自动维护更新时间时间戳
    */
    /*public function fromDateTime($value)
    {
        return strtotime(parent::fromDateTime($value));
    }*/

}
