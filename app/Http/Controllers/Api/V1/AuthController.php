<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/auth/login",
     *     operationId="login",
     *     tags={"authentication"},
     *     summary="login",
     *     description="login",
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for login",
     *         @OA\JsonContent(
     *             @OA\Property(property="phone", type="string", default="77012345678"),
     *             @OA\Property(property="password", type="string", default="Password1"),
     *             @OA\Property(property="password_confirmation", type="string", default="Password1"),
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
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $possibleUser = User::where('phone', $data['phone'])->first();

        if (! isset($possibleUser)) {
            return response()->json([
                'message' => 'Неправильный номер телефона'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (! password_verify($data['password'], $possibleUser->password)) {
            return response()->json([
                'message' => 'Неправильный пароль'
            ], Response::HTTP_UNAUTHORIZED);
        }

        /** @var \Illuminate\Contracts\Auth\StatefulGuard $auth */
        $auth = auth('api');

        if (! $token = $auth->attempt($data)) {
            return response()->json([
                'message' => 'Проблемы с системой авторизации'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'message' => 'Авторизация прошла успешно',
            'user' => new UserResource(auth('api')->user()),
            'token' => $this->tokenDataForResponse($token),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/auth/refresh",
     *     operationId="refresh",
     *     tags={"authentication"},
     *     summary="refresh",
     *     description="refresh",
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
    public function refresh(): JsonResponse
    {
        /** @var \Tymon\JWTAuth\JWTGuard $auth */
        $auth = auth('api');

        $newToken = $auth->refresh();

        return response()->json([
            'message' => 'Токен обновлен',
            'user' => new UserResource($auth->user()),
            'token' => $this->tokenDataForResponse($newToken),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     operationId="logout",
     *     tags={"authentication"},
     *     summary="logout",
     *     description="logout",
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
    public function logout(): JsonResponse
    {
        /** @var \Illuminate\Contracts\Auth\StatefulGuard $auth */
        $auth = auth();
        $auth->logout();

        return response()->json(['message' => 'Вы вышли из системы']);
    }
}
