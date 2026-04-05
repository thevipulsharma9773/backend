<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NumerologyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NumerologyController extends Controller
{
    public function __invoke(Request $request, NumerologyService $numerologyService): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
        ]);

        return response()->json([
            'data' => $numerologyService->calculate(
                $validated['full_name'],
                $validated['date_of_birth'],
            ),
        ]);
    }
}
