<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Hootlex\Friendships\Traits\Friendable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable, Friendable;
    
    protected $table='users';
    protected  $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','password_confirm',
    ];
    
    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
       return [];
    }
    
    
    public function events (){
        
        return $this->hasMany('App\Event','user_id','id');
        
        
    }
    
    
    public function tasks (){
        
        return $this->belongsToMany('App\Task','task_user','user_id','task_id');
        
        
    }
    
        public function invitaions (){
        
        return $this->hasMany('App\Invitation');
    }

        public function goings (){
        
        return $this->hasMany('App\Going','user_id','id');
    }
    
    
    public function friends(){

        return $this->belongsToMany('App\User','friend_user','user_id','friend_id');

    }

        public function suggests (){
        
        return $this->belongsToMany('App\User','suggest_user_friends','user_id','suggested_id');
    }
    
    public function task (){
        
        return $this->belongsTo('App\User');
    }
    
}
