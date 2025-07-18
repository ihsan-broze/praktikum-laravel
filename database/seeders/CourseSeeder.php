<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Category;
use App\Models\User;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $users = User::all();
        
        if ($categories->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Please run CategorySeeder and ensure users exist first.');
            return;
        }

        // Course data berdasarkan kategori yang ada
        $courseData = [
            // Programming Courses
            [
                'title' => 'Pemrograman Web dengan Laravel',
                'description' => 'Belajar membuat aplikasi web modern menggunakan framework Laravel PHP',
                'content' => 'Materi meliputi: MVC Pattern, Eloquent ORM, Blade Templating, Authentication, API Development',
                'category' => 'Programming',
                'level' => 'intermediate',
                'status' => 'published',
                'featured' => true,
                'duration' => 180
            ],
            [
                'title' => 'JavaScript ES6+ dan Modern Web Development',
                'description' => 'Menguasai JavaScript modern untuk pengembangan aplikasi web yang interaktif',
                'content' => 'Arrow Functions, Promises, Async/Await, Modules, Destructuring, Spread Operator',
                'category' => 'Programming',
                'level' => 'beginner',
                'status' => 'published',
                'featured' => false,
                'duration' => 120
            ],
            [
                'title' => 'React.js untuk Pemula',
                'description' => 'Membangun user interface yang dinamis dengan React.js',
                'content' => 'Components, Props, State, Hooks, Event Handling, Routing',
                'category' => 'Programming',
                'level' => 'beginner',
                'status' => 'draft',
                'featured' => false,
                'duration' => 150
            ],
            [
                'title' => 'Python untuk Data Science',
                'description' => 'Menggunakan Python untuk analisis data dan machine learning',
                'content' => 'NumPy, Pandas, Matplotlib, Seaborn, Scikit-learn basics',
                'category' => 'Programming',
                'level' => 'intermediate',
                'status' => 'published',
                'featured' => true,
                'duration' => 200
            ],

            // Desain Courses
            [
                'title' => 'UI/UX Design Fundamentals',
                'description' => 'Prinsip-prinsip dasar desain interface dan user experience',
                'content' => 'Design Thinking, Wireframing, Prototyping, User Research, Usability Testing',
                'category' => 'Desain',
                'level' => 'beginner',
                'status' => 'published',
                'featured' => true,
                'duration' => 160
            ],
            [
                'title' => 'Adobe Photoshop untuk Graphic Design',
                'description' => 'Menguasai tools Photoshop untuk desain grafis profesional',
                'content' => 'Layer Management, Selection Tools, Color Correction, Typography, Logo Design',
                'category' => 'Desain',
                'level' => 'intermediate',
                'status' => 'published',
                'featured' => false,
                'duration' => 140
            ],
            [
                'title' => 'Figma untuk Desain Interface',
                'description' => 'Membuat desain interface modern menggunakan Figma',
                'content' => 'Component Design, Auto Layout, Prototyping, Design System, Collaboration',
                'category' => 'Desain',
                'level' => 'beginner',
                'status' => 'published',
                'featured' => false,
                'duration' => 100
            ],

            // Business Courses
            [
                'title' => 'Digital Entrepreneurship',
                'description' => 'Membangun bisnis digital dari nol hingga sukses',
                'content' => 'Business Model Canvas, MVP Development, Customer Validation, Scaling Strategy',
                'category' => 'Business',
                'level' => 'intermediate',
                'status' => 'published',
                'featured' => true,
                'duration' => 180
            ],
            [
                'title' => 'Project Management Essentials',
                'description' => 'Mengelola proyek dengan efektif menggunakan metodologi modern',
                'content' => 'Agile, Scrum, Kanban, Risk Management, Team Leadership',
                'category' => 'Business',
                'level' => 'beginner',
                'status' => 'published',
                'featured' => false,
                'duration' => 120
            ],
            [
                'title' => 'Financial Planning for Startups',
                'description' => 'Perencanaan keuangan untuk startup dan usaha kecil',
                'content' => 'Budgeting, Cash Flow, Investment Planning, Financial Modeling',
                'category' => 'Business',
                'level' => 'advanced',
                'status' => 'draft',
                'featured' => false,
                'duration' => 160
            ],

            // Marketing Courses
            [
                'title' => 'Digital Marketing Strategy',
                'description' => 'Strategi pemasaran digital untuk era modern',
                'content' => 'SEO, SEM, Social Media Marketing, Content Marketing, Email Marketing',
                'category' => 'Marketing',
                'level' => 'intermediate',
                'status' => 'published',
                'featured' => true,
                'duration' => 170
            ],
            [
                'title' => 'Social Media Marketing Mastery',
                'description' => 'Menguasai pemasaran melalui platform media sosial',
                'content' => 'Instagram Marketing, Facebook Ads, TikTok Strategy, Influencer Marketing',
                'category' => 'Marketing',
                'level' => 'beginner',
                'status' => 'published',
                'featured' => false,
                'duration' => 130
            ],
            [
                'title' => 'Content Marketing & Copywriting',
                'description' => 'Membuat konten yang menarik dan copy yang converting',
                'content' => 'Content Strategy, Copywriting Techniques, Storytelling, Brand Voice',
                'category' => 'Marketing',
                'level' => 'intermediate',
                'status' => 'archived',
                'featured' => false,
                'duration' => 140
            ],

            // Data Science Courses
            [
                'title' => 'Introduction to Data Science',
                'description' => 'Pengenalan dunia data science dan aplikasinya',
                'content' => 'Data Analysis, Statistics, Python/R Programming, Data Visualization',
                'category' => 'Data Science',
                'level' => 'beginner',
                'status' => 'published',
                'featured' => true,
                'duration' => 190
            ],
            [
                'title' => 'Machine Learning with Python',
                'description' => 'Implementasi algoritma machine learning menggunakan Python',
                'content' => 'Supervised Learning, Unsupervised Learning, Neural Networks, Model Evaluation',
                'category' => 'Data Science',
                'level' => 'advanced',
                'status' => 'published',
                'featured' => false,
                'duration' => 220
            ],
            [
                'title' => 'Data Visualization with Tableau',
                'description' => 'Membuat visualisasi data yang efektif dengan Tableau',
                'content' => 'Dashboard Design, Interactive Charts, Data Storytelling, Best Practices',
                'category' => 'Data Science',
                'level' => 'intermediate',
                'status' => 'draft',
                'featured' => false,
                'duration' => 110
            ],

            // Other Courses
            [
                'title' => 'Personal Productivity & Time Management',
                'description' => 'Meningkatkan produktivitas dan mengelola waktu dengan efektif',
                'content' => 'Goal Setting, Prioritization, Time Blocking, Habit Formation',
                'category' => 'Other',
                'level' => 'beginner',
                'status' => 'published',
                'featured' => false,
                'duration' => 90
            ],
            [
                'title' => 'Communication Skills for Professionals',
                'description' => 'Meningkatkan kemampuan komunikasi di lingkungan profesional',
                'content' => 'Public Speaking, Presentation Skills, Interpersonal Communication, Conflict Resolution',
                'category' => 'Other',
                'level' => 'intermediate',
                'status' => 'published',
                'featured' => true,
                'duration' => 120
            ],
            [
                'title' => 'Photography Fundamentals',
                'description' => 'Dasar-dasar fotografi untuk pemula',
                'content' => 'Camera Settings, Composition Rules, Lighting Techniques, Photo Editing',
                'category' => 'Other',
                'level' => 'beginner',
                'status' => 'archived',
                'featured' => false,
                'duration' => 100
            ]
        ];

        foreach ($courseData as $index => $data) {
            // Cari category berdasarkan title
            $category = $categories->where('title', $data['category'])->first();
            
            if (!$category) {
                $this->command->warn("Category '{$data['category']}' not found. Skipping course: {$data['title']}");
                continue;
            }

            Course::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'content' => $data['content'],
                'category_id' => $category->id,
                'created_by' => $users->random()->id,
                'level' => $data['level'],
                'status' => $data['status'],
                'featured' => $data['featured'],
                'duration' => $data['duration'],
                'created_at' => now()->subDays(rand(1, 180)), // Random date dalam 6 bulan terakhir
                'updated_at' => now()->subDays(rand(1, 30))
            ]);

            $this->command->info("Created course: {$data['title']} in category: {$data['category']}");
        }

        $this->command->info('Course seeder completed successfully!');
    }
}