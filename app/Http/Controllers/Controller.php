<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Collection;

abstract class Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Eyva OpenApi Documentation",
     *      description="Eyva Swagger OpenApi Documentation",
     *      @OA\License(
     *          name="Apache 2.0",
     *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *      )
     * )
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Eyva API Server",
     *      @OA\SecurityScheme(
     *          securityScheme="bearerAuth",
     *          type="http",
     *          scheme="bearer",
     *      ),
     * )
     *
     * @OA\SecurityScheme(
     *     type="http",
     *     description="Login with phone and password to get the authentication token",
     *     name="Token based",
     *     in="header",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     securityScheme="apiAuth",
     * )
     */

    protected function tokenDataForResponse(string $token): array
    {
        /** @var \Tymon\JWTAuth\JWTGuard $auth */
        $auth = auth('api');

        return [
            'type' => 'bearer',
            'access_token' => $token,
            'expires_in' => $auth->factory()->getTTL(),
        ];
    }

    protected function storeFile(string $name): string
    {
        $timestamp = now()->format('Y_m_d_h_i_s');

        $file = request()->file($name);
        $filePath = uniqid($name) . '_' . $timestamp . '.' . $file->getClientOriginalExtension();
        $file->storeAs($name, $filePath, ['disk' => 'public']);

        return "$name/$filePath";
    }

    protected function groupSchedules($schedules): Collection
    {
        return $schedules->groupBy([
            function ($schedule) {
                return Carbon::parse($schedule->start_date)->format('Y-m-d'); 
            },
            function ($schedule) {
                return Carbon::parse($schedule->start_time)->format('H:i'); 
            }
        ]);
    }
}
