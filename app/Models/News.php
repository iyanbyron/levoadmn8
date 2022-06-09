<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use DateTimeInterface;
class News extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $table = 'user_gbook';
    protected $guard_name = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'content',
        'created_at',
        'updated_at',
        'user_id',
        'username',
        'admin_name',
        'reply_content',
        'is_reply',

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
