<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'type',  //0 - Individual 1 - Grupal
        'user_id_1', // Si type=0 -> contiene info Si type=1 -> NULL
        'user_id_2', // Si type=0 -> contiene info Si type=1 -> NULL
        'group_id',  // Si type=1 -> contiene info Si type=0 -> NULL
    ];

    //Get messages for the conversation
    public function messages()
    {
        return $this->hasMany('App\Models\Message')->orderBy('created_at','desc');
    }

    //Get usuario_id_1 for the conversation
    public function user_1()
    {
        return $this->belongsTo('App\User','user_id_1');
    }

    //Get usuario_id_2 for the conversation
    public function user_2()
    {
        return $this->belongsTo('App\User','user_id_2');
    }

    //Get Group for the conversation
    public function group()
    {
        return $this->belongsTo('App\Group','group_id');
    }
}
