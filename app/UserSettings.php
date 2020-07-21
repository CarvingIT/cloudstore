<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    protected $table = 'user_settings';
    
    public function user(){
        return $this->belongsTo('App\User');
    }
}
