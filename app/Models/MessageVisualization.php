<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageVisualization extends Model
{
    protected $fillable = [
        'message_id',
        'user_id',
        'read_at',
    ];

    //Get Message for the MessageVisualization
    public function message()
    {
        return $this->belongsTo('App\Models\Message');
    }

}
