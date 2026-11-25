<?php

namespace App\Http\Controllers;

use App\Services\Payments\Contracts\PaymentGateway;
use App\Services\Payments\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class PaymobWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        PaymentGateway $gateway,
        PaymentService $payments
    ): JsonResponse {
        try {
            $result = $gateway->parseWebhook($request);
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'Invalid callback'], 403);
        }

        $payments->handleWebhook($result);

        return response()->json(['message' => 'ok']);
    }
}
