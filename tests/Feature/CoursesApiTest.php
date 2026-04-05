<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoursesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_courses_endpoint_returns_course_data(): void
    {
        $category = Category::query()->create([
            'name' => 'Numerology',
            'slug' => 'numerology',
        ]);

        $course = Course::query()->create([
            'category_id' => $category->id,
            'title' => 'Numerology Basics',
            'slug' => 'numerology-basics',
            'description' => 'Intro course',
            'price' => 999,
            'is_premium' => true,
        ]);

        Lesson::query()->create([
            'course_id' => $course->id,
            'title' => 'Lesson 1',
            'video_url' => 'https://example.com/video',
            'sort_order' => 1,
            'is_preview' => true,
        ]);

        $response = $this->getJson('/api/courses');

        $response->assertOk()
            ->assertJsonFragment([
                'title' => 'Numerology Basics',
                'category' => 'Numerology',
            ]);
    }
}
