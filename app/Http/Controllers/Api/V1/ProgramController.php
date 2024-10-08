<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\UserProgramResource;
use App\Models\Program;
use App\Models\Transaction;
use App\Repositories\UserProgramRepository;
use App\Services\PaymentStrategy\PaymentContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProgramController extends Controller
{
    public function __construct(private UserProgramRepository $userProgramRepository)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/programs",
     *     operationId="programs",
     *     tags={"programs"},
     *     summary="Get programs",
     *     description="Get programs",
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
        $data = Program::with(['photos'])
            ->isEnabled()
            ->orderBy('id')
            ->get();

        return response()->json(['data' => ProgramResource::collection($data)]);
    }

    /**
     * @OA\Get(
     *     path="/programs/{id}",
     *     operationId="showprograms",
     *     tags={"programs"},
     *     summary="Get one by id from programs",
     *     description="Get one by id from programs",
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
        $data = Program::with([
            'programServices.service.activeSchedules.myFeedback',
            'programServices.service.myFeedback',
            'photos',
        ])
        ->findOrFail($id);

        return response()->json(['data' => new ProgramResource($data)]);
    }

    /**
     * @OA\Post(
     *     path="/programs/{program_id}/buy",
     *     operationId="buyprograms",
     *     tags={"programs"},
     *     summary="buy",
     *     description="buy",
     *     security={{"apiAuth": {} }},
     * 
     *     @OA\Parameter(
     *         in="path",
     *         name="program_id",
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
    public function buy(int $program_id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth('api')->user();

        $program = Program::with(['programServices', 'photos'])
            ->isEnabled()
            ->find($program_id);

        if (!isset($program)) {
            return response()->json([
                'message' => 'Программа не найдена',
            ], Response::HTTP_NOT_FOUND);
        }

        $paymentContext = new PaymentContext([
            'price' => $program->price,
            'transaction_type' => Transaction::TYPE_PURCHASE_PROGRAM,
        ], $user);

        try {
            DB::beginTransaction();

            $paymentContext->setPaymentStrategy('balance');
            $paymentContext->executeStrategy();
            $userProgram = $this->userProgramRepository->create($program, $user);
            $paymentContext->bindTransaction($userProgram, $user->transactions()->latest()->first());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage(), $e->getTrace());

            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() !== 0 ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'data' => new UserProgramResource($userProgram->load([
                'programServices.programService.service.myFeedback',
                'program',
            ])),
        ]);
    }
}
