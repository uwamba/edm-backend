<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    // The model this factory corresponds to
    protected $model = \App\Models\User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => 'admin@example.com', // fixed email for seeding
            'email_verified_at' => now(),
            'password' => Hash::make('password123'), // default password
            'remember_token' => Str::random(10),
        ];
    }
}
