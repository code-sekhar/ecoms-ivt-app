<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class UserProfile extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'image',
        'user_id',
        'status'
    ];


    public function user(){
        return $this->belongsTo(User::class);
    }
}
