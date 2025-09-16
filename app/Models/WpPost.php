<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpPost extends Model
{
    protected $fillable = ['wp_id','title','content','status','priority'];

}
