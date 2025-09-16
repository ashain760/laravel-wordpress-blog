<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpUser extends Model
{
    protected $fillable = ['wordpress_id','name','email','access_token','refresh_token','token_expires_at'];
    protected $casts = ['token_expires_at' => 'datetime'];
    protected $hidden = ['access_token','refresh_token'];

    // encrypt token when saving
    public function setAccessTokenAttribute($val){
        $this->attributes['access_token'] = encrypt($val);
    }
    public function getAccessTokenAttribute($val){
        return decrypt($val);
    }
}
