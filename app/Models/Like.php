<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    
    protected $fillable =[
        'user_id',
        'liked_by',
        'likeable_type'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function likeable(){
        return $this->morphTo();
    }
}
