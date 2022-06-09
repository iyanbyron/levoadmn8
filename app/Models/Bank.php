<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class  Bank extends Model
{
    public $timestamps = true;
    protected $table = 'bank';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bank_name',
        'status',
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

}
