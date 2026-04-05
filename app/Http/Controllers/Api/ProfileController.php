<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $purchasedCourses = $user->payments()
            ->with('course.category')
            ->where('status', 'completed')
            ->latest('verified_at')
            ->get()
            ->pluck('course')
            ->filter()
            ->unique('id')
            ->values();

        return response()->json([
            'user' => $user,
            'purchased_courses' => CourseResource::collection($purchasedCourses),
        ]);
    }
}
