<?php

namespace App;

use App\Models\UserPosition;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // 'name', 'email', 'password',
        'name',
        'surname',
        'grade',
        'dni',
        'location_id',
        'user_name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //'password', 'remember_token',
        //'user_name',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        //'email_verified_at' => 'datetime',
    ];

    //Get messages_sent for the user
    public function messages_sent()
    {
        return $this->hasMany('App\Models\Message', 'sender_id')->orderBy('created_at','desc');
    }

    //Get Positions for the User
    public function positions()
    {
        return $this->hasMany('App\Models\UserPosition')->orderBy('created_at','desc');
    }

    public function getUserLastPositionAttribute()
    {
        $lastPosition = UserPosition::where('user_id',$this->id)->orderBy('created_at','desc')->first();

        return $lastPosition;
    }
}
