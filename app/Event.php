<?php

namespace App;
use Illuminate\Notifications\Notifiable;


use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use Notifiable;

    protected $table='events';
    
protected $primaryKey = 'id';
    
    public function user (){
        
        return $this->belongTo('App\User','id','user_id');
    }
    
    
    
        public function tasks (){
        
        return $this->hasMany('App\Task');
    }

        public function invitaions (){
        
        return $this->hasMany('App\Invitation');
    }
    
    
        public function goings (){
        
        return $this->hasMany('App\Going','event_id','id');
    }
    
        public function goings_count(){
            
            return $this->goings()->count();
        }
    
    
}
