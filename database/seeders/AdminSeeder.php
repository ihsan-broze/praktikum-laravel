<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing admin users (optional)
        User::where('email', 'admin@admin.com')->delete();
        
        // Create new admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        echo "Admin user created successfully!\n";
        echo "Email: admin@admin.com\n";
        echo "Password: admin\n";

        // Generate QR for admin (if method exists)
        if (method_exists($admin, 'generateProfileQR')) {
            $admin->generateProfileQR();
        }

        // Create regular user
        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'password' => Hash::make('user'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        // Generate QR for user (if method exists)
        if (method_exists($user, 'generateProfileQR')) {
            $user->generateProfileQR();
        }

        $this->command->info('Admin and User accounts created successfully!');
        $this->command->info('Admin: admin@admin.com / admin');
        $this->command->info('User: user@example.com / user');
        $this->command->info('QR codes generated for both users');
    }
}   