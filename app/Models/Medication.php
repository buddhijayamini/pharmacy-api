<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'description', 'quantity'
    ];

     // Define the relationship with the User model
     public function user()
     {
         return $this->belongsTo(User::class);
     }

     // Define the relationship with the Customer model
     public function customer()
     {
         return $this->belongsTo(Customer::class,'customer_id');
     }
}
