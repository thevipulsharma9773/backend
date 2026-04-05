<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CourseController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $courses = Course::query()
            ->with('category')
            ->withCount('lessons')
            ->latest()
            ->get();

        return CourseResource::collection($courses);
    }

    public function show(Course $course): CourseResource
    {
        $course->load([
            'category',
            'lessons' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        return new CourseResource($course);
    }
}
