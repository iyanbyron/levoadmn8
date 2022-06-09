<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationLog extends Model
{
    protected $table = 'operation_log';
    protected $fillable = ['user_id', 'path', 'method', 'ip', 'type', 'input', 'agent', 'platform', 'browser'];

    public static $methodColors = [
        'GET' => '#43d543',
        'POST' => '#75751c',
        'PUT' => 'blue',
        'DELETE' => 'red',
        'OPTIONS' => 'hotpink',
        'PATCH' => 'thistle',
        'LINK' => 'mintcream',
        'UNLINK' => 'firebrick',
        'COPY' => 'lightcyan',
        'HEAD' => 'gray',
        'PURGE' => 'copper',
    ];

    public static $methods = [
        'GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH', 'LINK', 'UNLINK', 'COPY', 'HEAD', 'PURGE'
    ];


    /**
     * Log belongs to users.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\AdminUser', 'user_id', 'id');
    }

}
