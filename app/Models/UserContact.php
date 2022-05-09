<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserContact extends Model
{
    protected $fillable = [
        'user_id',
        'contact_type',
        'contact_id',
    ];

    //Get the owning contact model (USER o Group).
    public function contact()
    {
        return $this->morphTo();
    }

}
