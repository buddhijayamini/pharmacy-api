<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'email', 'phone'
    ];

     // Define the relationship with the User model
     public function user()
     {
         return $this->belongsTo(User::class);
     }

     public function medications()
     {
         return $this->hasMany(Medication::class);
     }
}
