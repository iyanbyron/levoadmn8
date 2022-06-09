<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Push extends Model
{
    protected $table = 'push';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'push_title',
        'push_content',
        'push_jump_type',
        'push_sig',
        'push_url',
        'push_way',
        'push_uid',
        'push_app_type',
    ];
}
