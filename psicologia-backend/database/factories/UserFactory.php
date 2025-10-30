<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'rol' => fake()->randomElement(['TOE', 'PSICOLOGO', 'TUTOR', 'DIRECTOR']),
            'estado' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function toe()
    {
        return $this->state(fn (array $attributes) => ['rol' => 'TOE']);
    }

    public function psicologo()
    {
        return $this->state(fn (array $attributes) => ['rol' => 'PSICOLOGO']);
    }

    public function tutor()
    {
        return $this->state(fn (array $attributes) => ['rol' => 'TUTOR']);
    }

    public function director()
    {
        return $this->state(fn (array $attributes) => ['rol' => 'DIRECTOR']);
    }
}
