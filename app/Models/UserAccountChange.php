<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class UserAccountChange extends Model
{
  //  public $timestamps = false;
   /* const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';*/
    protected $table = 'user_account_change';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'actual_name',
        'type',
        'game_name',
        'play',
        'issue',
        'bet',
        'order_num',
        'bet_money',
        'money',
        'operator',
        'remark',
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
