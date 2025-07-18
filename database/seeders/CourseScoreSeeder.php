<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseScore;
use App\Models\Course;
use App\Models\User;

class CourseScoreSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();
        $users = User::all();

        if ($courses->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Please ensure courses and users exist first.');
            return;
        }

        $difficulties = ['easy', 'medium', 'hard'];
        
        // Generate random scores for testing
        for ($i = 0; $i < 100; $i++) {
            $course = $courses->random();
            $user = $users->random();
            
            // Skip if combination already exists
            if (CourseScore::where('course_id', $course->id)->where('user_id', $user->id)->exists()) {
                continue;
            }

            $score = rand(60, 100);
            $completionPercentage = rand(70, 100);
            
            CourseScore::create([
                'course_id' => $course->id,
                'user_id' => $user->id,
                'score' => $score,
                'review' => $this->generateRandomReview($score),
                'difficulty_rating' => $difficulties[array_rand($difficulties)],
                'completion_percentage' => $completionPercentage,
                'completed_at' => $completionPercentage == 100 ? now()->subDays(rand(1, 30)) : null,
                'created_at' => now()->subDays(rand(1, 60))
            ]);
        }

        $this->command->info('Course scores seeded successfully!');
    }

    private function generateRandomReview($score)
    {
        $reviews = [
            90 => ['Excellent course!', 'Outstanding content and delivery', 'Highly recommended'],
            80 => ['Very good course', 'Well structured and informative', 'Good learning experience'],
            70 => ['Decent course', 'Average content quality', 'Some room for improvement'],
            60 => ['Below expectations', 'Could be better organized', 'Needs more practical examples']
        ];

        $range = $score >= 90 ? 90 : ($score >= 80 ? 80 : ($score >= 70 ? 70 : 60));
        $reviewOptions = $reviews[$range];
        
        return $reviewOptions[array_rand($reviewOptions)];
    }
}