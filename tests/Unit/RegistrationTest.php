<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->post('auth.register', [
            'name' => 'John Doe',
            'username' => 'John',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role_id' => 1
        ]);

        return $response;
        //$response->assertStatus(201);
    }
}
