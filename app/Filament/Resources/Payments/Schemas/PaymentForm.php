<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\PaymentStatus;
use App\Models\Course;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Details')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label('User')
                            ->options(User::query()->pluck('email', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('course_id')
                            ->label('Course')
                            ->options(Course::query()->pluck('title', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('INR'),
                        Select::make('status')
                            ->options(PaymentStatus::class)
                            ->required(),
                        TextInput::make('gateway')
                            ->required()
                            ->default('razorpay'),
                        DateTimePicker::make('verified_at'),
                        TextInput::make('gateway_order_id'),
                        TextInput::make('gateway_payment_id'),
                        KeyValue::make('meta')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
