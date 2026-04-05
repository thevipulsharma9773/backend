<?php

namespace App\Filament\Resources\Lessons\Schemas;

use App\Models\Course;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Lesson Details')
                    ->columns(2)
                    ->schema([
                        Select::make('course_id')
                            ->label('Course')
                            ->options(Course::query()->pluck('title', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(1),
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('video_url')
                            ->label('Video URL')
                            ->url()
                            ->required()
                            ->columnSpanFull(),
                        Toggle::make('is_preview')
                            ->label('Preview lesson')
                            ->inline(false)
                            ->default(false),
                    ]),
            ]);
    }
}
