<?php

namespace App\Http\Controllers;

use App\Requests\Schedule\StoreRequest;
use App\Services\ScheduleService;
use App\Models\SchedulePerUser;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function index() {
        $query = SchedulePerUser::where('user_id', auth()->user()->id )->orderBy('day','ASC')->get();
        return $query;
    }
    public function store(StoreRequest $request) {


        $query = $this->scheduleService->store( $request->schedule );

        return response()->json([
            'message' => 'Schedule registered successfully',
            'user' => $query,
        ], 201);

    }
}
