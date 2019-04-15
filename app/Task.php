<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table='tasks';
    protected  $primaryKey = 'id';
    
    public function event (){
        
        return $this->belongsTo('App\Event');
    }
    
    
    public function users (){
        
        return $this->belongsToMany('App\Task','task_user','task_id','user_id');
        
        
    }
    
    public function user (){
        
        return $this->hasMany('App\Task');
    }
    
}
