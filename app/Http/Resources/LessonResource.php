<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $course = $this->course;
        $user = $request->user();
        $hasUnlockedCourse = $course && (
            ! $course->is_premium ||
            ($user && $course->payments()->where('user_id', $user->id)->where('status', 'completed')->exists())
        );

        return [
            'id' => $this->id,
            'title' => $this->title,
            'video_url' => $hasUnlockedCourse || $this->is_preview ? $this->video_url : null,
            'sort_order' => $this->sort_order,
            'is_preview' => $this->is_preview,
            'is_locked' => ! $hasUnlockedCourse && ! $this->is_preview,
        ];
    }
}
