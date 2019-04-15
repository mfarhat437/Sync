<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $table='invitations';
    
    
    public function event(){
        
        return $this->belongsTo('App\Event');
    }
        
        
    
    public function user(){
        
        return $this->belongsTo('App\User');
    }
    
}
