<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class  Userlogs extends Model
{
    public $timestamps = true;
    protected $table = 'user_logs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'created_at',
        'updated_at',
        'login_ip',
        'browser',
        'login_addr',
        'login_domain'

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

}
