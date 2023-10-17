<?php

namespace App\Services;

use App\Http\Requests\RequestCreateWorkScheduleAdvise;
use App\Repositories\DepartmentRepository;
use App\Repositories\ExampleInterface;
use App\Repositories\UserRepository;
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
            $doctor = UserRepository::doctorOfHospital(['id_doctor' => $request->id_doctor])->first();
            if(empty($doctor)) return $this->responseError(400, 'Không tìm thấy bác sĩ !');
            $hospital = UserRepository::findUserById($doctor->id_hospital);

            $time = $request->time;
            $filter = [
                'time' => $time,
                'id_doctor' => $request->id_doctor,
            ];
            $findWorkSchedule = WorkScheduleRepository::getWorkSchedule($filter)->count();
            if($findWorkSchedule > 0) return $this->responseError(400, 'Lịch này đã được đặt !');

            $startTime = $time['interval'][0];
            $endTime = $time['interval'][1];
            $date = $time['date'];

            $data = [
                'id_user' => $user->id,
                'id_doctor' => $request->id_doctor,
                'id_service' => null,
                'time' => json_encode($time),
                'content' => "Bạn có lịch tư vấn với bác sĩ $doctor->name_doctor thuộc chuyên khoa" .
                "$doctor->name_department của bệnh viện $hospital->name vào khoản thời gian từ lúc $startTime cho đến" .
                "$endTime của ngày $date tại địa chỉ $hospital->address.  SĐT Liên hệ bệnh viện : $hospital->phone ." 
            ];
            $workSchedule = WorkScheduleRepository::createWorkSchedule($data);
            $workSchedule->time = json_decode($workSchedule->time);
            return $this->responseOK(201, $workSchedule, 'Đặt lịch tư vấn thành công !');
        } catch (Throwable $e) {
            return $this->responseError(400, $e->getMessage());
        }
    }
    // sdt hospital chắc chắn có vì khi register là require 

}
