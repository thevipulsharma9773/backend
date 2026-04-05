<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@vastuapp.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ],
        );

        $categories = collect([
            'Vastu Foundations',
            'Numerology Mastery',
            'Premium Consultations',
        ])->mapWithKeys(fn (string $name) => [
            $name => Category::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            ),
        ]);

        $courses = [
            [
                'category' => 'Vastu Foundations',
                'title' => 'Vastu Compass Essentials',
                'description' => 'Learn how to use directions, room placement, and entry alignment for everyday Vastu analysis.',
                'price' => 0,
                'is_premium' => false,
                'lessons' => [
                    ['title' => 'Understanding North and Energy Flow', 'video_url' => 'https://example.com/videos/vastu-1', 'is_preview' => true],
                    ['title' => 'Practical Compass Reading at Home', 'video_url' => 'https://example.com/videos/vastu-2', 'is_preview' => true],
                ],
            ],
            [
                'category' => 'Numerology Mastery',
                'title' => 'Numerology for Name and Birth Date',
                'description' => 'Decode life path and destiny patterns with a guided, practical numerology course.',
                'price' => 1499,
                'is_premium' => true,
                'lessons' => [
                    ['title' => 'Life Path Number Deep Dive', 'video_url' => 'https://example.com/videos/numero-1', 'is_preview' => true],
                    ['title' => 'Advanced Name Number Reading', 'video_url' => 'https://example.com/videos/numero-2', 'is_preview' => false],
                ],
            ],
            [
                'category' => 'Premium Consultations',
                'title' => 'Home Energy Audit Workshop',
                'description' => 'A premium workshop on identifying and fixing common spatial imbalances using Vastu principles.',
                'price' => 2499,
                'is_premium' => true,
                'lessons' => [
                    ['title' => 'Audit Checklist', 'video_url' => 'https://example.com/videos/audit-1', 'is_preview' => true],
                    ['title' => 'Premium Remedies and Layout Planning', 'video_url' => 'https://example.com/videos/audit-2', 'is_preview' => false],
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            $course = Course::query()->updateOrCreate(
                ['slug' => Str::slug($courseData['title'])],
                [
                    'category_id' => $categories[$courseData['category']]->id,
                    'title' => $courseData['title'],
                    'description' => $courseData['description'],
                    'price' => $courseData['price'],
                    'thumbnail' => null,
                    'is_premium' => $courseData['is_premium'],
                ],
            );

            foreach ($courseData['lessons'] as $index => $lessonData) {
                Lesson::query()->updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'title' => $lessonData['title'],
                    ],
                    [
                        'video_url' => $lessonData['video_url'],
                        'sort_order' => $index + 1,
                        'is_preview' => $lessonData['is_preview'],
                    ],
                );
            }
        }
    }
}
