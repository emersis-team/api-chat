<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Message extends Model
{
    protected $fillable = [
        'message',
        'conversation_id',
        'sender_id',
        'message_type',
        'message_id',
    ];

    protected $appends = [
        'display_type'
    ];

    protected $with = [
        'message',
    ];

    public function message()
    {
        return $this->morphTo();
    }

    //Get conversation for the message
    public function conversation()
    {
        return $this->belongsTo('App\Models\Conversation');
    }

    //Get sender for the message
    public function sender()
    {
        return $this->belongsTo('App\User', 'sender_id');
    }

    //Get MessageVisualizations for the message
    public function message_visualizations()
    {
        return $this->hasMany('App\Models\MessageVisualization')->orderBy('created_at','desc');
    }

    public function getDisplayTypeAttribute() {

        if($this->message_type == "App\\Models\\TextMessage"){
            return "TextMessage";
        }else if($this->message_type == "App\\Models\\FileMessage"){
            return "FileMessage";
        }else if($this->message_type == "App\\Models\\PositionMessage"){
            return "PositionMessage";
        }

    }
}
