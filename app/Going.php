<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Going extends Model
{
    
    protected $fillable = [];
    protected $table='goings';
    
    
    public function event(){
        
        return $this->belongsTo('App\Event','id','event_id');
    }
    
    
    
    public function user(){
        
        return $this->belongsTo('App\User','id','user_id');
    }
    
}
