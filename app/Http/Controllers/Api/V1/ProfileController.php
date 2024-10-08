<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\UpdateProfileRequest;
use App\Http\Requests\Api\Profile\AssetsRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserAbonnementPresentResource;
use App\Http\Resources\UserAbonnementResource;
use App\Http\Resources\UserProgramResource;
use App\Http\Resources\UserResource;
use App\Models\ServiceSchedule;
use App\Models\Transaction;
use App\Models\UserAbonnement;
use App\Models\UserAbonnementPresent;
use App\Models\UserProgram;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/profile",
     *     operationId="profile",
     *     tags={"profile"},
     *     summary="Get authenticated profile",
     *     description="Get authenticated profile",
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
    public function showProfile(): JsonResponse
    {
        return response()->json([
            'data' => new UserResource(auth('api')->user()),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/profile",
     *     operationId="updateProfile",
     *     tags={"profile"},
     *     summary="updateProfile",
     *     description="updateProfile",
     *     security={{"apiAuth": {} }},
     * 
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     description="photo",
     *                     property="photo",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         format="binary",
     *                     ),
     *                 ),
     *                 @OA\Property(
     *                     description="name",
     *                     property="name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     description="birth_date",
     *                     property="birth_date",
     *                     type="date",
     *                 ),
     *             ),
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
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth('api')->user();
        $data = $request->validated();

        if (isset($data['photo'])) {
            $data['photo'] = $this->storeFile('photo');
        }

        if (! $user->update($data)) {
            return response()->json([
                'message' => 'Произошла ошибка при обновлении профиля'
            ], Response::HTTP_NO_CONTENT);
        }

        return response()->json([
            'data' => new UserResource(auth('api')->user()),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/profile/assets",
     *     operationId="profileassets",
     *     tags={"profile"},
     *     summary="Get assets",
     *     description="Get assets",
     *     security={{"apiAuth": {} }},
     * 
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     description="service_schedule_id",
     *                     property="service_schedule_id",
     *                     type="integer",
     *                 ),
     *             ),
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
    public function assets(AssetsRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = auth('api')->user();

        $serviceSchedule = ServiceSchedule::with(['service'])
            ->findOrFail($validated['service_schedule_id']);

        $abonnements = UserAbonnement::where('user_id', $user->id)
            ->where('expiration_date', '>=', Carbon::parse($serviceSchedule->start_date_time))
            ->where('minutes', '>', $serviceSchedule->service->duration)
            ->get();

        $presents = UserAbonnementPresent::whereHas('userAbonnement',
            function ($query) use ($user, $serviceSchedule) {
                $query->where('user_id', $user->id)
                    ->where('expiration_date', '>=', Carbon::parse($serviceSchedule->start_date_time));
            })
            ->where('service_id', $serviceSchedule->service_id)
            ->where('visits', '>', 0)
            ->get();

        $programs = UserProgram::with(['programServices.service'])
            ->where('user_id', $user->id)
            ->withSum('programServices', 'visits')
            ->withSum('programServices', 'old_visits')
            ->where('expiration_date', '>=', Carbon::parse($serviceSchedule->start_date_time))
            ->whereHas('programServices', function ($query) use ($serviceSchedule) {
                $query->where('visits', '>', 0)
                    ->where('service_id', $serviceSchedule->service_id);
            })
            ->get();

        return response()->json([
            'data' => [
                'abonnements' => UserAbonnementResource::collection($abonnements),
                'presents' => UserAbonnementPresentResource::collection($presents),
                'programs' => UserProgramResource::collection($programs),
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/profile/abonnements/current",
     *     operationId="profilecurrentabonnements",
     *     tags={"profile"},
     *     summary="Get current abonnements",
     *     description="Get current abonnements",
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
    public function abonnementsCurrent(): JsonResponse
    {
        $data = UserAbonnement::with(['abonnement', 'presents.service.myFeedback'])
            ->withCount('presents')
            ->where('user_id', auth('api')->user()->id)
            ->whereDate('expiration_date', '>', now())
            ->orderBy('expiration_date', 'desc')
            ->get();

        return response()->json([
            'data' => UserAbonnementResource::collection($data),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/profile/abonnements/history",
     *     operationId="profilehistoryabonnements",
     *     tags={"profile"},
     *     summary="Get history abonnements",
     *     description="Get history abonnements",
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
    public function abonnementsHistory(): JsonResponse
    {
        $data = UserAbonnement::with(['abonnement', 'presents.service.myFeedback'])
            ->withCount('presents')
            ->where('user_id', auth('api')->user()->id)
            ->whereDate('expiration_date', '<=', now())
            ->orderBy('expiration_date', 'desc')
            ->get();

        return response()->json([
            'data' => UserAbonnementResource::collection($data),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/profile/programs/current",
     *     operationId="profileCurrentPrograms",
     *     tags={"profile"},
     *     summary="Get user related current programs",
     *     description="Get user related current programs",
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
    public function programsCurrent(): JsonResponse
    {
        $data = UserProgram::with(['program', 'programServices.service.myFeedback'])
            ->withSum('programServices', 'visits')
            ->withSum('programServices', 'old_visits')
            ->where('user_id', auth('api')->user()->id)
            ->whereDate('expiration_date', '>', now())
            ->orderBy('expiration_date', 'desc')
            ->get();

        return response()->json([
            'data' => UserProgramResource::collection($data),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/profile/programs/history",
     *     operationId="profileHistoryPrograms",
     *     tags={"profile"},
     *     summary="Get user related history programs",
     *     description="Get user related history programs",
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
    public function programsHistory(): JsonResponse
    {
        $data = UserProgram::with(['program', 'programServices.service.myFeedback'])
            ->where('user_id', auth('api')->user()->id)
            ->whereDate('expiration_date', '<=', now())
            ->orderBy('expiration_date', 'desc')
            ->get();

        return response()->json([
            'data' => UserProgramResource::collection($data),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/profile/transactions",
     *     operationId="transactions",
     *     tags={"profile"},
     *     summary="Get transactions",
     *     description="Get transactions",
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
    public function transactions(): JsonResponse
    {
        $transactions = Transaction::where('user_id', auth('api')->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => TransactionResource::collection($transactions),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/profile/photo",
     *     operationId="deletephoto",
     *     tags={"profile"},     
     *     summary="Delete user photo",
     *     description="Delete user photo",
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
    public function deletePhoto(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth('api')->user();

        if (isset($user->photo)) {
            $deleted = Storage::delete($user->photo);

            if ($deleted) {
                $user->photo = null;
                $user->save();
            }
        }

        return response()->json([
            'message' => 'Photo deleted successfully',
            'data' => new UserResource($user->refresh()),
        ]);
    }
}
