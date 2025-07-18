<?php

// tests/TestCase.php
namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure we have an application key for testing
        if (empty(config('app.key'))) {
            config(['app.key' => 'base64:' . base64_encode('32-character-random-string-here')]);
        }
        
        // Run migrations for each test
        $this->artisan('migrate:fresh');
        
        // Optional: Seed database if needed
        // $this->artisan('db:seed');
    }

    /**
     * Clean up after tests.
     */
    protected function tearDown(): void
    {
        // Clean up any uploaded files during testing
        $this->cleanupTestFiles();
        
        parent::tearDown();
    }

    /**
     * Clean up test files.
     */
    private function cleanupTestFiles(): void
    {
        $disk = \Storage::disk('public');
        
        // Clean up test avatars
        $avatarFiles = $disk->files('avatars');
        foreach ($avatarFiles as $file) {
            if (str_contains($file, 'test') || str_contains($file, 'fake')) {
                $disk->delete($file);
            }
        }
        
        // Clean up test post images
        $postFiles = $disk->files('posts/featured-images');
        foreach ($postFiles as $file) {
            if (str_contains($file, 'test') || str_contains($file, 'fake')) {
                $disk->delete($file);
            }
        }
    }
}

