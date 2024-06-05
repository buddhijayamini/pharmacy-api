<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; // Import the trait
use Spatie\Permission\Models\Role; // Import the Role model

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at'
    ];

    // Define roles as constants
    // public const ROLE_OWNER = 'owner';
    // public const ROLE_MANAGER = 'manager';
    // public const ROLE_CASHIER = 'cashier';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    // Define the relationship with the Customer model
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    // Define the relationship with medications
    public function medications(): HasMany
    {
        return $this->hasMany(Medication::class);
    }

    // Define the relationship with the Role model
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function getStoredRole()
    {
        // Retrieve the role using the Spatie Permission package's methods
        return Role::find($this->role_id);
    }
}
