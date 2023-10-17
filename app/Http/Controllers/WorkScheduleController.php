<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestCreateWorkScheduleAdvise;
use App\Services\WorkScheduleService;

class WorkScheduleController extends Controller
{
    protected WorkScheduleService $workScheduleService;

    public function __construct(WorkScheduleService $workScheduleService)
    {
        $this->workScheduleService = $workScheduleService;
    }

    public function add(RequestCreateWorkScheduleAdvise $request)
    {
        return $this->workScheduleService->add($request);
    }

}
