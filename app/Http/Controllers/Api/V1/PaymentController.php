<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Payment\CreatePaymentRequest;
use App\Services\AcquiringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private readonly AcquiringService $acquiringService,
    ) {
    }

    /**
     * @OA\Post(
     *     path="/payment/create",
     *     operationId="paymentcreate",
     *     tags={"payment"},
     *     summary="payment create",
     *     description="payment create",
     *     security={{"apiAuth": {} }},
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for login",
     *         @OA\JsonContent(
     *             @OA\Property(property="eyv_amount", type="integer", default="100"),
     *         ),
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="HTTP_OK",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="HTTP_BAD_REQUEST",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="HTTP_UNAUTHORIZED",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="HTTP_INTERNAL_SERVER_ERROR",
     *     ),
     * )
     */
    public function createPayment(CreatePaymentRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth('api')->user();
        $data = $request->validated();

        $response = $this->acquiringService->createPayment(
            $user,
            $data['eyv_amount'],
        );

        $status = $response['status'];
        unset($response['status']);

        return response()->json($response, $status);
    }

    public function setPaymentStatus(Request $request): JsonResponse
    {
        return response()->json(
            $this->acquiringService->setPaymentStatus($request->all())
        );
    }
}
