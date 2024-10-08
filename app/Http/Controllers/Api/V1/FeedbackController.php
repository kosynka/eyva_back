<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\StoreRequest;
use App\Http\Resources\FeedbackResource;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FeedbackController extends Controller
{
    /**
     * @OA\Get(
     *     path="/feedbacks",
     *     operationId="feedbacks",
     *     tags={"feedbacks"},
     *     summary="Get my feedbacks",
     *     description="Get my feedbacks",
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
    public function indexMyFeedbacks(): JsonResponse
    {
        $data = Feedback::where('user_id', auth('api')->user()->id)
            ->orderBy('id')
            ->get();

        return response()->json(['data' => FeedbackResource::collection($data)]);
    }

    /**
     * @OA\Post(
     *     path="/feedbacks/",
     *     operationId="storefeedbacks",
     *     tags={"feedbacks"},
     *     summary="Store new data into feedbacks",
     *     description="Store new data into feedbacks",
     *     security={{"apiAuth": {} }},
     * 
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="integer",
     *                 property="stars",
     *                 example="1"
     *             ),
     *             @OA\Property(
     *                 type="integer",
     *                 property="program_id",
     *                 example="1"
     *             ),
     *             @OA\Property(
     *                 type="integer",
     *                 property="service_id",
     *                 example="1"
     *             ),
     *             @OA\Property(
     *                 type="integer",
     *                 property="schedule_id",
     *                 example="1"
     *             ),
     *             @OA\Property(
     *                 type="string",
     *                 property="body",
     *                 example="some text example for body"
     *             ),
     *         )
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
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth('api')->user()->id;

        $feedbackableFields = array_filter($data, function ($key) {
            return in_array($key, ['program_id', 'service_id', 'schedule_id']);
        }, ARRAY_FILTER_USE_KEY);

        if (count($feedbackableFields) !== 1) {
            return response()->json([
                'message' => 'Отзыв можно оставить только для одной сущности',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($this->searchDuplicate($data) !== null) {
            return response()->json([
                'message' => 'Вы уже оставили отзыв',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $item = Feedback::create($data);

        return response()->json(['data' => new FeedbackResource($item)]);
    }

    private function searchDuplicate(array $data): ?Feedback
    {
        $check['user_id'] = $data['user_id'];

        if (isset($data['program_id'])) {
            $check['program_id'] = $data['program_id'];
        } else if (isset($data['service_id'])) {
            $check['service_id'] = $data['service_id'];
        } else if (isset($data['schedule_id'])) {
            $check['schedule_id'] = $data['schedule_id'];
        } else {
            return null;
        }

        return Feedback::where($check)->first();
    }
}
