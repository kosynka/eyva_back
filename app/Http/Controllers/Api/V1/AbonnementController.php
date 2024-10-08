<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AbonnementResource;
use App\Http\Resources\UserAbonnementResource;
use App\Models\Abonnement;
use App\Models\Transaction;
use App\Repositories\UserAbonnementRepository;
use App\Services\PaymentStrategy\PaymentContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AbonnementController extends Controller
{
    public function __construct(private UserAbonnementRepository $userAbonnementRepository)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/abonnements",
     *     operationId="abonnements",
     *     tags={"abonnements"},
     *     summary="Get abonnements",
     *     description="Get abonnements",
     *     security={{"apiAuth": {} }},
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
    public function index(): JsonResponse
    {
        $data = Abonnement::withCount('presents')
            ->with(['presents.service.myFeedback', 'photos'])
            ->isEnabled()
            ->orderBy('id')
            ->get();

        return response()->json(['data' => AbonnementResource::collection($data)]);
    }

    /**
     * @OA\Get(
     *     path="/abonnements/{id}",
     *     operationId="showabonnements",
     *     tags={"abonnements"},
     *     summary="Get one by id from abonnements",
     *     description="Get one by id from abonnements",
     *     security={{"apiAuth": {} }},
     * 
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="integer"),
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
    public function show(int $id): JsonResponse
    {
        $data = Abonnement::withCount('presents')
            ->with(['presents.service.myFeedback', 'photos'])
            ->isEnabled()
            ->findOrFail($id);

        return response()->json(['data' => new AbonnementResource($data)]);
    }

    /**
     * @OA\Post(
     *     path="/abonnements/{abonnement_id}/buy",
     *     operationId="buyabonnements",
     *     tags={"abonnements"},
     *     summary="buy",
     *     description="buy",
     *     security={{"apiAuth": {} }},
     * 
     *     @OA\Parameter(
     *         in="path",
     *         name="abonnement_id",
     *         required=true,
     *         @OA\Schema(type="integer"),
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
    public function buy(int $abonnement_id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth('api')->user();

        $abonnement = Abonnement::with(['presents', 'photos'])
            ->isEnabled()
            ->find($abonnement_id);

        if (!isset($abonnement)) {
            return response()->json([
                'message' => 'Абонемент не найден',
            ], Response::HTTP_NOT_FOUND);
        }

        $paymentContext = new PaymentContext([
            'price' => $abonnement->price,
            'transaction_type' => Transaction::TYPE_PURCHASE_ABONNEMENT,
        ], $user);

        try {
            DB::beginTransaction();

            $paymentContext->setPaymentStrategy('balance');
            $paymentContext->executeStrategy();
            $userAbonnement = $this->userAbonnementRepository->create($abonnement, $user);
            $paymentContext->bindTransaction($userAbonnement, $user->transactions()->latest()->first());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage(), $e->getTrace());

            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() !== 0 ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['data' => new UserAbonnementResource($userAbonnement->load('abonnement.photos'))]);
    }
}
