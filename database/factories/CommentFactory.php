<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'content' => $this->faker->paragraph(3),
            'author_name' => $this->faker->name,
            'author_email' => $this->faker->safeEmail,
            'status' => 'approved', // Default ke approved untuk test yang sederhana
            'post_id' => Post::factory(),
            'user_id' => $this->faker->boolean(70) ? User::factory() : null,
            'parent_id' => null,
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    public function spam(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'spam',
        ]);
    }

    public function byUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
            'author_name' => $user->name,
            'author_email' => $user->email,
        ]);
    }

    public function asGuest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'author_name' => $this->faker->name,
            'author_email' => $this->faker->safeEmail,
        ]);
    }

    public function asReply(Comment $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'post_id' => $parent->post_id,
        ]);
    }
}