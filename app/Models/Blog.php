<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Like;

class Blog extends Model
{
    use HasFactory;

    protected $fillable =[
        'user_id',
        'title',
        'description',
        'image',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function likes(){
        return $this->morphMany(Like::class, 'likeable');
    }

    // Accessor to check if the current logged-in user liked the blog
    public function getIsLikedAttribute(){
        if(auth()->check()){
            return $this->likes()->where('user_id', auth()->id())->exists();
        }
        return false;
    }

    // Append 'is_liked' to the model's array form
    protected $appends = ['is_liked']; 
}
