<?php

namespace App\Services;

use App\Http\Requests\RequestCreateWorkScheduleAdvise;
use App\Repositories\ExampleInterface;
use App\Repositories\WorkScheduleInterface;
use App\Repositories\WorkScheduleRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Throwable;

class WorkScheduleService
{
    protected WorkScheduleInterface $workScheduleRepository;

    public function __construct(
        WorkScheduleInterface $workScheduleRepository
    ) {
        $this->workScheduleRepository = $workScheduleRepository;
    }

    public function responseOK($status = 200, $data = null, $message = '')
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'status' => $status,
        ], $status);
    }

    public function responseError($status = 400, $message = '')
    {
        return response()->json([
            'message' => $message,
            'status' => $status,
        ], $status);
    }

    public function add(RequestCreateWorkScheduleAdvise $request)
    {
        try {
            $user = Auth::user();
            $request->merge([
                'id_user' => $user->id,
                'id_service' => null,
                'time' => json_encode($request->time),
            ]);
            $workSchedule = WorkScheduleRepository::createWorkSchedule($request->all());
            $workSchedule->time = json_decode($workSchedule->time);
            return $this->responseOK(201, $workSchedule, 'Đặt lịch tư vấn thành công !');
        } catch (Throwable $e) {
            return $this->responseError(400, $e->getMessage());
        }
    }

}
