<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Service\EnrollmentRequest;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\UserServiceScheduleResource;
use App\Models\Service;
use App\Models\ServiceSchedule;
use App\Models\Transaction;
use App\Models\UserServiceSchedule;
use App\Repositories\UserServiceRepository;
use App\Repositories\UserServiceScheduleRepository;
use App\Services\PaymentStrategy\PaymentContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ServiceController extends Controller
{
    public function __construct(
        private UserServiceRepository $userServiceRepository,
        private UserServiceScheduleRepository $userServiceScheduleRepository,
    ) {
    }

    /**
     * @OA\Get(
     *     path="/services",
     *     operationId="services",
     *     tags={"services"},
     *     summary="Get services",
     *     description="Get services",
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
        $data = Service::where('type', '!=', Service::TYPE_MASTERCLASS)
            ->with(['photos'])
            ->orderBy('id')
            ->get();

        return response()->json(['data' => ServiceResource::collection($data)]);
    }

    /**
     * @OA\Get(
     *     path="/services/{id}",
     *     operationId="showservices",
     *     tags={"services"},
     *     summary="Get one by id from services",
     *     description="Get one by id from services",
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
        $item = Service::with([
            'instructors',
            'programServices',
            'photos',
            'activeSchedules.myFeedback',
            'profitablePrograms.photos',
            'myFeedback',
        ])->findOrFail($id);

        return response()->json(['data' => new ServiceResource($item)]);
    }

    /**
     * @OA\Post(
     *     path="/services/{service_id}/enroll",
     *     operationId="enrollService",
     *     tags={"services"},
     *     summary="enroll",
     *     description="enroll",
     *     security={{"apiAuth": {} }},
     * 
     *     @OA\Parameter(
     *         in="path",
     *         name="service_id",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for enroll",
     *         @OA\JsonContent(
     *             @OA\Property(property="service_schedule_id", type="integer", default="1"),
     *             @OA\Property(property="user_program_id", type="integer"),
     *             @OA\Property(property="user_abonnement_id", type="integer"),
     *             @OA\Property(property="user_abonnement_present_id", type="integer"),
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
    public function enroll(EnrollmentRequest $request, int $service_id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth('api')->user();
        $data = $request->validated();

        if (count($data) > 2) {
            return response()->json([
                'message' => 'Вы выбрали сразу несколько методов оплаты. Пожалуйста, выберите один из них',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $schedule = ServiceSchedule::with('service')
            ->findOrFail($data['service_schedule_id']);

        if ($schedule->service_id !== $service_id) {
            return response()->json([
                'message' => 'Данная услуга не имеет расписание в это время',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        if ($schedule->places_count_left < 1) {
            return response()->json([
                'message' => 'Все места уже заняты',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        if ($schedule->start_date_time->lessThanOrEqualTo(now()->addHours(1))) {
            return response()->json([
                'message' => 'Время для записи истекло, выберите другое время',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        if ($this->userServiceScheduleRepository->hasIntersectingEnrollements($user, $schedule)) {
            return response()->json([
                'message' => 'Время для записи пересекается с другими записями',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $existingEnrollment = UserServiceSchedule::where([
            'user_id' => $user->id,
            'service_schedule_id' => $data['service_schedule_id']
        ])->first();

        if (isset($existingEnrollment)) {
            return response()->json([
                'message' => 'Вы уже записаны на эту дату и время',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $data['service_id'] = $service_id;
        $data['transaction_type'] = Transaction::getServicePurchaseType($schedule->service->type);
        $data['price'] = $schedule->service->price;
        $data['duration'] = $schedule->service->duration;

        $paymentContext = new PaymentContext($data, $user);

        if (isset($data['user_program_id'])) {
            $paymentContext->setPaymentStrategy('program');
        } else if (isset($data['user_abonnement_id'])) {
            $paymentContext->setPaymentStrategy('abonnement');
        } else if (isset($data['user_abonnement_present_id'])) {
            $paymentContext->setPaymentStrategy('abonnement_present');
        } else {
            $paymentContext->setPaymentStrategy('balance');
        }

        try {
            DB::beginTransaction();

            $type = $paymentContext->executeStrategy();
            $enrollment = $this->userServiceRepository->enroll($schedule, $user, $type);

            if ($type === UserServiceSchedule::TYPE_PRIMARY) {
                $paymentContext->bindTransaction($enrollment, $user->transactions()->latest()->first());
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage(), $e->getTrace());

            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() !== 0 ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'data' => new UserServiceScheduleResource($enrollment->load('schedule'))
        ]);
    }
}
