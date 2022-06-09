<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    protected $table = 'ads';
    //protected $guard_name = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        'ads_pic',
        'ads_status',
        'ads_url',
        'ads_title',
        'video_bigclass_id',
        'navigation_smallclass_id',
        'ads_position',
        'ads_show_time',
        'video_smallclass_id'

    ];


}
