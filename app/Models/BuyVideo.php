<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyVideo extends Model
{
    protected $table = 'buy_video';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'video_id'
    ];


}
