<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Enrollment\RescheduleRequest;
use App\Http\Resources\UserServiceScheduleResource;
use App\Models\ServiceSchedule;
use App\Models\UserServiceSchedule;
use App\Repositories\UserServiceScheduleRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnrollmentController extends Controller
{
    public function __construct(private UserServiceScheduleRepository $userServiceScheduleRepository)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/enrollments/current",
     *     operationId="indexcurrentenrollments",
     *     tags={"enrollments"},
     *     summary="Get current enrollments",
     *     description="Get current enrollments",
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
    public function indexCurrent(): JsonResponse
    {
        $data = UserServiceSchedule::withAggregate('schedule', 'start_date')
            ->with(['schedule.service.myFeedback', 'schedule.myFeedback'])
            ->where('status', UserServiceSchedule::STATUS_ENROLLED)
            ->where('user_id', auth('api')->user()->id)
            ->orderBy('schedule_start_date', 'asc')
            ->get();

        return response()->json([
            'data' => $this->groupSchedules(UserServiceScheduleResource::collection($data)),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/enrollments/history",
     *     operationId="indexhistoryenrollments",
     *     tags={"enrollments"},
     *     summary="Get history enrollments",
     *     description="Get history enrollments",
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
    public function indexHistory(): JsonResponse
    {
        $data = UserServiceSchedule::withAggregate('schedule', 'start_date')
            ->with(['schedule.service.myFeedback', 'schedule.myFeedback'])
            ->whereIn('status', [UserServiceSchedule::STATUS_FINISHED, UserServiceSchedule::STATUS_SKIPPED])
            ->where('user_id', auth('api')->user()->id)
            ->orderBy('schedule_start_date', 'asc')
            ->get();

        return response()->json([
            'data' => $this->groupSchedules(UserServiceScheduleResource::collection($data)),
        ]);
    }

    /**
     * @OA\Get( 
     *     path="/enrollments/{id}",
     *     operationId="showenrollments",
     *     tags={"enrollments"},
     *     summary="Get one by id from enrollments",
     *     description="Get one by id from enrollments",
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
    public function show(int $id): JsonResponse
    {
        $data = UserServiceSchedule::with(['schedule.myFeedback', 'schedule.service.activeSchedules', 'schedule.service'])
            ->findOrFail($id);

        return response()->json(['data' => new UserServiceScheduleResource($data)]);
    }

    /**
     * @OA\Post(
     *     path="/enrollments/{id}/reschedule",
     *     operationId="enrollmentsReschedule",
     *     tags={"enrollments"},
     *     summary="reschedule",
     *     description="reschedule",
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
    public function reschedule(RescheduleRequest $request, int $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth('api')->user();
        $data = $request->validated();

        $oldEnrollment = UserServiceSchedule::where([
            'user_id' => $user->id,
            'service_schedule_id' => $data['service_schedule_id'],
        ])->first();

        $enrollment = UserServiceSchedule::with(['schedule'])
            ->where([
                'id' => $id,
                'user_id' => $user->id,
            ])
            ->firstOrFail();

        $newSchedule = ServiceSchedule::with(['service.myFeedback', 'myFeedback'])
            ->findOrFail($data['service_schedule_id']);

        if (Carbon::parse($enrollment->schedule->start_date_time)->isBefore(now()->addHours(24))) {
            return response()->json([
                'message' => 'Изменение даты возможно не позднее чем за 24 часа до начала сеанса'
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        if (isset($oldEnrollment)) {
            return response()->json([
                'message' => 'Вы уже записаны на это время',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        if ($newSchedule->service_id !== $enrollment->schedule->service_id) {
            return response()->json([
                'message' => 'При смене времени записи нельзя менять тип услуги',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        if ($newSchedule->start_date_time->lessThanOrEqualTo(now())) {
            return response()->json([
                'message' => 'Время для записи истекло, выберите другое время',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        if ($newSchedule->places_count_left < 1) {
            return response()->json([
                'message' => 'Все места уже заняты',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        if ($this->userServiceScheduleRepository->hasIntersectingEnrollements($user, $newSchedule)) {
            return response()->json([
                'message' => 'Время для записи пересекается с другими записями',
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        try {
            DB::beginTransaction();

            $enrollment->schedule->places_count_total += 1;
            $enrollment->schedule->places_count_left += 1;
            $enrollment->schedule->save();

            $enrollment->service_schedule_id = $newSchedule->id;
            $enrollment->save();

            $newSchedule->places_count_total -= 1;
            $newSchedule->places_count_left -= 1;
            $newSchedule->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage(), $e->getTrace());

            return response()->json([
                'message' => 'Произошла ошибка ' . $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'data' => new UserServiceScheduleResource($enrollment->refresh())
        ]);
    }

    protected function groupSchedules($schedules): Collection
    {
        return $schedules->groupBy([
            function ($userSchedule) {
                return Carbon::parse($userSchedule->schedule->start_date)->format('Y-m-d'); 
            },
            function ($userSchedule) {
                return Carbon::parse($userSchedule->schedule->start_time)->format('H:i'); 
            }
        ]);
    }
}
