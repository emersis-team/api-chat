<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name',
    ];

    //Get users for the group
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

}
