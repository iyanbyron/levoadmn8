<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class AgentUserType extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $table = 'agent_user_type';
    protected $guard_name = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pid', 'name', 'childs'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

}
