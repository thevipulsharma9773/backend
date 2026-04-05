<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $hasAccess = ! $this->is_premium || ($user && $this->payments()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->exists());

        return [
            'id' => $this->id,
            'category' => $this->category?->name,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (string) $this->price,
            'thumbnail' => $this->thumbnail ? asset('storage/'.$this->thumbnail) : null,
            'is_premium' => $this->is_premium,
            'has_access' => $hasAccess,
            'lessons_count' => $this->lessons_count ?? $this->lessons()->count(),
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
        ];
    }
}
