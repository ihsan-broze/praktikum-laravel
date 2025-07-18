<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Fix: Use Hash::make instead of hardcoded hash
            'remember_token' => Str::random(10),
            'role' => 'user', // Default role
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    public function moderator(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'moderator',
        ]);
    }

    public function withRole($role): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => $role,
        ]);
    }

    public function withPreferences(array $preferences = [])
    {
        return $this->state(function (array $attributes) use ($preferences) {
            return [
                'preferences' => array_merge([
                    'theme' => 'light',
                    'notifications' => true,
                    'language' => 'en'
                ], $preferences),
            ];
        });
    }

    public function withAvatar()
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            $user->update(['avatar' => 'avatars/test-avatar.jpg']);
        });
    }
}