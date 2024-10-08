<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\Page\ProposalRequest;
use App\Http\Requests\Api\Page\SearchRequest;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\ProgramResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\PageElementResource;
use App\Http\Resources\ServiceScheduleResource;
use App\Mail\ProposalMail;
use App\Models\PageElement;
use App\Models\Program;
use App\Models\Service;
use App\Models\ServiceSchedule;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/pages/schedules",
     *     operationId="schedules",
     *     tags={"pages"},
     *     summary="Get schedules",
     *     description="Get schedules",
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
    public function schedules(): JsonResponse
    {
        $lightSchedules = ServiceSchedule::with('service.myFeedback', 'myFeedback')
            ->whereHas('service', function ($query) {
                $query->where('type', '<>', Service::TYPE_MASTERCLASS);
            })
            ->where('hall', ServiceSchedule::HALL_LIGHT)
            ->where('places_count_left', '>', 0)
            ->whereRaw('(start_date + start_time::interval) >= ?', [now()->addHours(1)->toDateTimeString()])
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $darkSchedules = ServiceSchedule::with('service.myFeedback', 'myFeedback')
            ->whereHas('service', function ($query) {
                $query->where('type', '<>', Service::TYPE_MASTERCLASS);
            })
            ->where('hall', ServiceSchedule::HALL_DARK)
            ->where('places_count_left', '>', 0)
            ->whereRaw('(start_date + start_time::interval) >= ?', [now()->addHours(1)->toDateTimeString()])
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json([
            'light' => $this->groupSchedules(ServiceScheduleResource::collection($lightSchedules)),
            'dark' => $this->groupSchedules(ServiceScheduleResource::collection($darkSchedules)),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/pages/header",
     *     operationId="header",
     *     tags={"pages"},
     *     summary="Get main page header elements",
     *     description="Get main page header elements",
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
    public function mainPageHeader(): JsonResponse
    {
        $data = PageElement::where('page_type', PageElement::PAGE_TYPE_MAIN)
            ->orderBy('key')
            ->orderBy('weight')
            ->get();

        return response()->json([
            'data' => PageElementResource::collection($data),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/pages/about-us",
     *     operationId="about-us",
     *     tags={"pages"},
     *     summary="Get about us page elements",
     *     description="Get about us page elements",
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
    public function aboutUs(): JsonResponse
    {
        $data = PageElement::where('page_type', PageElement::PAGE_TYPE_ABOUT_US)
            ->orderBy('key')
            ->orderBy('weight')
            ->get();

        $employees = User::where('role', User::ROLE_EMPLOYEE)
            ->get();

        return response()->json([
            'about_us' => PageElementResource::collection($data),
            'employees' => EmployeeResource::collection($employees),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/pages/search",
     *     operationId="search",
     *     tags={"pages"},
     *     summary="Get search",
     *     description="Get search",
     *     security={{"apiAuth": {} }},
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for search",
     *         @OA\JsonContent(
     *             @OA\Property(property="query", type="string"),
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
    public function search(SearchRequest $request): JsonResponse
    {
        $query = $request->validated()['query'];

        return response()->json([
            'services' => ServiceResource::collection(
                Service::with(['photos', 'myFeedback'])->search($query)->get()
            ),
            'programs' => ProgramResource::collection(
                Program::with(['photos', 'myFeedback'])
                    ->isEnabled()
                    ->search($query)
                    ->get()
            ),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/pages/proposal",
     *     operationId="proposal",
     *     tags={"pages"},
     *     summary="Send a proposal",
     *     description="Send a proposal",
     *     security={{"apiAuth": {} }},
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"subject", "message"},
     *             @OA\Property(property="subject", type="string", example="Request Subject"),
     *             @OA\Property(property="message", type="string", example="This is the message content.")
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
    public function proposal(ProposalRequest $request): JsonResponse
    {
        $data = $request->validated();

        Mail::send(new ProposalMail($data));
        
        return response()->json([
            'message' => 'Заявка успешно отправлена',
        ]);
    }
}
