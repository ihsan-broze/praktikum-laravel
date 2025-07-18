<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(6, true);
        
        return [
            'title' => $title,
            'content' => fake()->paragraphs(10, true),
            'excerpt' => fake()->paragraph(3),
            'status' => 'draft',
            'published_at' => null,
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'meta_title' => $title,
            'meta_description' => fake()->sentence(20),
            'featured_image' => null,
            'views' => fake()->numberBetween(0, 1000),
            'allow_comments' => fake()->boolean(80), // 80% chance of allowing comments
        ];
    }

    /**
     * Indicate that the post is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Indicate that the post is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    /**
     * Indicate that the post has a featured image.
     */
    public function withFeaturedImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured_image' => 'posts/featured-images/test-image.jpg',
        ]);
    }

    /**
     * Create a post for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create a post for a specific category.
     */
    public function inCategory(Category $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $category->id,
        ]);
    }

    /**
     * Create a post with a specific title (useful for testing slug generation).
     */
    public function withTitle(string $title): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => $title,
        ]);
    }

    /**
     * Create a post with comments disabled.
     */
    public function withoutComments(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_comments' => false,
        ]);
    }

    /**
     * Create a post with comments enabled.
     */
    public function withComments(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_comments' => true,
        ]);
    }

    /**
     * Create a post with zero views.
     */
    public function withNoViews(): static
    {
        return $this->state(fn (array $attributes) => [
            'views' => 0,
        ]);
    }

    /**
     * Create a post with specific view count.
     */
    public function withViews(int $views): static
    {
        return $this->state(fn (array $attributes) => [
            'views' => $views,
        ]);
    }
}