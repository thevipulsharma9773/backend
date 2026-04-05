<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Course;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Str;

class RazorpayService
{
    public function createOrder(User $user, Course $course): Payment
    {
        $amountInPaise = (int) round((float) $course->price * 100);
        $orderId = 'order_'.Str::upper(Str::random(14));

        return Payment::query()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => $course->price,
            'status' => PaymentStatus::Pending,
            'gateway' => 'razorpay',
            'gateway_order_id' => $orderId,
            'meta' => [
                'currency' => config('services.razorpay.currency', 'INR'),
                'amount_in_paise' => $amountInPaise,
                'notes' => [
                    'course_title' => $course->title,
                    'user_email' => $user->email,
                ],
            ],
        ]);
    }

    public function verifySignature(string $orderId, string $paymentId, string $signature): bool
    {
        $secret = (string) config('services.razorpay.key_secret');

        if ($secret === '') {
            return false;
        }

        $generated = hash_hmac('sha256', "{$orderId}|{$paymentId}", $secret);

        return hash_equals($generated, $signature);
    }
}
