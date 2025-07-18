<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure we have an app key for testing
        if (empty(config('app.key'))) {
            config(['app.key' => 'base64:' . base64_encode('this-is-a-32-character-key-for-test')]);
        }
        
        $this->user = User::factory()->create([
            'password' => Hash::make('password')
        ]);
    }

    /** @test */
    public function user_model_can_be_created()
    {
        $this->assertInstanceOf(User::class, $this->user);
        $this->assertDatabaseHas('users', [
            'email' => $this->user->email
        ]);
    }

    /** @test */
    public function user_password_is_hashed()
    {
        $this->assertTrue(Hash::check('password', $this->user->password));
        $this->assertNotEquals('password', $this->user->password);
    }

    /** @test */
    public function user_can_have_avatar()
    {
        $this->user->update(['avatar' => 'avatars/test.jpg']);
        
        // Refresh the model to get updated data
        $this->user->refresh();
        
        $this->assertEquals('avatars/test.jpg', $this->user->avatar);
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'avatar' => 'avatars/test.jpg'
        ]);
    }

    /** @test */
    public function user_can_have_preferences()
    {
        $preferences = [
            'theme' => 'dark',
            'language' => 'en',
            'notifications' => [
                'email' => true,
                'push' => false
            ]
        ];

        $this->user->update(['preferences' => $preferences]);
        $this->user->refresh();

        $this->assertEquals($preferences, $this->user->preferences);
    }

    /** @test */
    public function user_can_have_role()
    {
        $this->user->update(['role' => 'admin']);
        $this->user->refresh();
        
        $this->assertEquals('admin', $this->user->role);
        $this->assertTrue($this->user->hasRole('admin'));
        $this->assertTrue($this->user->isAdmin());
    }

    /** @test */
    public function user_role_defaults_to_user()
    {
        $newUser = User::factory()->create();
        
        $this->assertEquals('user', $newUser->role);
        $this->assertFalse($newUser->isAdmin());
        $this->assertFalse($newUser->isModerator());
    }

    /** @test */
    public function user_can_be_moderator()
    {
        $moderator = User::factory()->moderator()->create();
        
        $this->assertEquals('moderator', $moderator->role);
        $this->assertTrue($moderator->isModerator());
        $this->assertFalse($moderator->isAdmin());
    }

    /** @test */
    public function user_avatar_url_returns_correct_path()
    {
        $this->user->update(['avatar' => 'avatars/test.jpg']);
        $this->user->refresh();

        $expectedUrl = asset('storage/avatars/test.jpg');
        $this->assertEquals($expectedUrl, $this->user->avatar_url);
    }

    /** @test */
    public function user_avatar_url_returns_null_when_no_avatar()
    {
        $this->assertNull($this->user->avatar_url);
    }

    /** @test */
    public function user_preferences_are_cast_to_array()
    {
        $preferences = ['theme' => 'dark'];
        $this->user->update(['preferences' => $preferences]);
        
        $this->user->refresh();
        $this->assertIsArray($this->user->preferences);
        $this->assertEquals($preferences, $this->user->preferences);
    }

    /** @test */
    public function password_is_automatically_hashed()
    {
        $plainPassword = 'new-password';
        $this->user->update(['password' => $plainPassword]);
        
        $this->user->refresh();
        $this->assertNotEquals($plainPassword, $this->user->password);
        $this->assertTrue(Hash::check($plainPassword, $this->user->password));
    }

    /** @test */
    public function user_factory_creates_valid_user()
    {
        $user = User::factory()->create();
        
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);
        $this->assertNotNull($user->email_verified_at);
        $this->assertEquals('user', $user->role);
        $this->assertIsArray($user->preferences);
        $this->assertArrayHasKey('theme', $user->preferences);
        $this->assertArrayHasKey('language', $user->preferences);
        $this->assertArrayHasKey('notifications', $user->preferences);
    }

    /** @test */
    public function admin_factory_creates_admin_user()
    {
        $admin = User::factory()->admin()->create();
        
        $this->assertEquals('admin', $admin->role);
        $this->assertTrue($admin->isAdmin());
    }

    /** @test */
    public function unverified_factory_creates_unverified_user()
    {
        $user = User::factory()->unverified()->create();
        
        $this->assertNull($user->email_verified_at);
    }

    /** @test */
    public function user_can_be_soft_deleted()
    {
        $userId = $this->user->id;
        
        $this->user->delete();
        
        $this->assertSoftDeleted('users', ['id' => $userId]);
        
        // Verify user still exists in database but with deleted_at timestamp
        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'email' => $this->user->email,
        ]);
        
        // Verify user is not found in normal queries
        $this->assertNull(User::find($userId));
        
        // Verify user can be found with trashed
        $this->assertNotNull(User::withTrashed()->find($userId));
    }

    /** @test */
    public function email_must_be_unique()
    {
        $email = 'test@example.com';
        User::factory()->create(['email' => $email]);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['email' => $email]);
    }

    /** @test */
    public function user_timestamps_are_working()
    {
        $this->assertNotNull($this->user->created_at);
        $this->assertNotNull($this->user->updated_at);
        
        $originalUpdatedAt = $this->user->updated_at;
        
        // Wait a moment and update
        sleep(1);
        $this->user->update(['name' => 'Updated Name']);
        
        $this->assertNotEquals($originalUpdatedAt, $this->user->fresh()->updated_at);
    }

    /** @test */
    public function user_preferences_have_default_structure()
    {
        $user = User::factory()->create();
        
        $this->assertArrayHasKey('theme', $user->preferences);
        $this->assertArrayHasKey('language', $user->preferences);
        $this->assertArrayHasKey('notifications', $user->preferences);
        
        $this->assertEquals('light', $user->preferences['theme']);
        $this->assertEquals('en', $user->preferences['language']);
        $this->assertIsArray($user->preferences['notifications']);
    }

    /** @test */
    public function user_can_have_custom_preferences()
    {
        $customPreferences = [
            'theme' => 'dark',
            'language' => 'es',
            'timezone' => 'America/New_York',
            'notifications' => [
                'email' => false,
                'push' => true,
                'sms' => true,
            ]
        ];

        $user = User::factory()->withPreferences($customPreferences)->create();
        
        $this->assertEquals($customPreferences, $user->preferences);
    }

    /** @test */
    public function user_with_avatar_factory_works()
    {
        $user = User::factory()->withAvatar()->create();
        
        $this->assertNotNull($user->avatar);
        $this->assertEquals('avatars/test-avatar.jpg', $user->avatar);
        $this->assertNotNull($user->avatar_url);
    }
}