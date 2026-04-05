<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\InitiatePaymentRequest;
use App\Http\Requests\Payment\VerifyPaymentRequest;
use App\Models\Course;
use App\Models\Payment;
use App\Services\RazorpayService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    public function __construct(private readonly RazorpayService $razorpayService)
    {
    }

    public function initiate(InitiatePaymentRequest $request): JsonResponse
    {
        $course = Course::query()->findOrFail($request->integer('course_id'));

        if (! $course->is_premium || (float) $course->price <= 0) {
            return response()->json([
                'message' => 'This course does not require payment.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $payment = $this->razorpayService->createOrder($request->user(), $course);

        return response()->json([
            'message' => 'Payment initiated successfully.',
            'data' => [
                'payment_id' => $payment->id,
                'order_id' => $payment->gateway_order_id,
                'amount' => $payment->meta['amount_in_paise'],
                'currency' => $payment->meta['currency'],
                'razorpay_key' => config('services.razorpay.key_id'),
                'course' => [
                    'id' => $course->id,
                    'title' => $course->title,
                ],
            ],
        ]);
    }

    public function verify(VerifyPaymentRequest $request): JsonResponse
    {
        $payment = Payment::query()
            ->whereKey($request->integer('payment_id'))
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $isValid = $this->razorpayService->verifySignature(
            $request->string('razorpay_order_id')->value(),
            $request->string('razorpay_payment_id')->value(),
            $request->string('razorpay_signature')->value(),
        );

        $payment->update([
            'status' => $isValid ? PaymentStatus::Completed : PaymentStatus::Failed,
            'gateway_order_id' => $request->string('razorpay_order_id')->value(),
            'gateway_payment_id' => $request->string('razorpay_payment_id')->value(),
            'verified_at' => $isValid ? now() : null,
            'meta' => array_merge($payment->meta ?? [], [
                'signature' => $request->string('razorpay_signature')->value(),
            ]),
        ]);

        return response()->json([
            'message' => $isValid ? 'Payment verified successfully.' : 'Payment verification failed.',
            'data' => [
                'payment_id' => $payment->id,
                'status' => $payment->status->value,
                'course_unlocked' => $isValid,
            ],
        ], $isValid ? Response::HTTP_OK : Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
