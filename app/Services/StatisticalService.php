<?php

namespace App\Services;

use App\Repositories\HospitalDepartmentRepository;
use App\Repositories\HospitalServiceRepository;
use App\Repositories\InforDoctorRepository;
use App\Repositories\InforHospitalRepository;
use App\Repositories\InforUserRepository;
use App\Repositories\UserRepository;
use App\Repositories\WorkScheduleRepository;
use Throwable;

class StatisticalService
{
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

    public function getStatisticalAccount()
    {
        try {
            $totalAccount = UserRepository::getTotalAccount();
            $totalUserAccount = InforUserRepository::getTotalUserAccount();
            $totalDoctorAccount = InforDoctorRepository::getTotalDoctorAccount(0);
            $totalHospitalAccount = InforHospitalRepository::getTotalHospitalAccount();
            $statistical = [
                'totalAccount' => $totalAccount,
                'totalUserAccount' => $totalUserAccount,
                'totalDoctorAccount' => $totalDoctorAccount,
                'totalHospitalAccount' => $totalHospitalAccount,
            ];
            if ($statistical) {
                return $this->responseOK(200, $statistical, 'Xem thống kê admin thành công !');
            }
        } catch (Throwable $e) {
            return $this->responseError(400, $e->getMessage());
        }
    }

    public function getStatisticalAccountByTime($startDay, $endDay)
    {
        try {
            // $minDate = \Carbon\Carbon::create(1001, 1, 1, 0, 0, 0);
            // $maxDate = \Carbon\Carbon::create(2999, 1, 1, 0, 0, 0);

            // $inputDate = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $startDay);
            // if ($inputDate >= $minDate && $inputDate <= $maxDate) return $this->responseError(400, "Ngày hợp lệ!");

            // $inputDate = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $endDay);
            // if ($inputDate >= $minDate && $inputDate <= $maxDate) return $this->responseError(400, "Ngày hợp lệ!");

            $totalUserAccount = InforUserRepository::getUserAccountByTime($startDay, $endDay);
            $totalDoctorAccount = InforDoctorRepository::getTotalDoctorAccountByTime($startDay, $endDay);
            $totalHospitalAccount = InforHospitalRepository::getTotalHospitalAccountByTime($startDay, $endDay);
            $statistical = [
                'totalUserAccount' => $totalUserAccount,
                'totalDoctorAccount' => $totalDoctorAccount,
                'totalHospitalAccount' => $totalHospitalAccount,
            ];
            if ($statistical) {
                return $this->responseOK(200, $statistical, 'Xem thống kê admin thành công !');
            }
        } catch (Throwable $e) {
            return $this->responseError(400, $e->getMessage());
        }
    }

    public function getStatisticalAccountHospital($hospitalId)
    {
        try {
            $totalDoctorAccount = InforDoctorRepository::getTotalDoctorAccount($hospitalId);

            $doctorIds = InforDoctorRepository::getIdDoctor($hospitalId);
            $workScheduleCount = WorkScheduleRepository::getTotalWorkScheduleHospital($doctorIds);

            $totalHospitalDepartment = HospitalDepartmentRepository::getTotalDepartmentHospital($hospitalId);

            $hospitalDepartmentIds = HospitalDepartmentRepository::getArrDepartmentHospital($hospitalId);
            $totalHospitalService = HospitalServiceRepository::getTotalServiceHospital($hospitalDepartmentIds);

            $statistical = [
                'totalDoctorAccount' => $totalDoctorAccount,
                'totalWorkSchedule' => $workScheduleCount,
                'totalHospitalDepartment' => $totalHospitalDepartment,
                'totalHospitalService' => $totalHospitalService,
            ];
            if ($statistical) {
                return $this->responseOK(200, $statistical, 'Xem thống kê hospital thành công !');
            }
        } catch (Throwable $e) {
            return $this->responseError(400, $e->getMessage());
        }
    }

    public function getStatisticalHospitalDoctor($hospitalId, $startDay, $endDay)
    {
        try {
            $doctorIds = InforDoctorRepository::getIdDoctor($hospitalId);
            $workScheduleCount = WorkScheduleRepository::getTotalWorkScheduleHospitalDoctor($doctorIds, $startDay, $endDay);

            $statistical = [

                'totalWorkSchedule' => $workScheduleCount,

            ];
            if ($statistical) {
                return $this->responseOK(200, $statistical, 'Xem thống kê hospital thành công !');
            }
        } catch (Throwable $e) {
            return $this->responseError(400, $e->getMessage());
        }
    }

    public function getStatisticalHospital($hospitalId, $startDay, $endDay)
    {
        try {
            $doctorIds = InforDoctorRepository::getIdDoctor($hospitalId);
            $workScheduleCount = WorkScheduleRepository::getWorkScheduleHospitalService($doctorIds, $startDay, $endDay);

            $statistical = [

                'totalWorkSchedule' => $workScheduleCount,

            ];
            if ($statistical) {
                return $this->responseOK(200, $statistical, 'Xem thống kê hospital thành công !');
            }
        } catch (Throwable $e) {
            return $this->responseError(400, $e->getMessage());
        }
    }

    public function getStatisticalDoctor($doctorId)
    {
        try {
            $totalWorkSchedule = WorkScheduleRepository::getTotalWorkScheduleDoctorByDay($doctorId, 0);
            $totalWorkSchedule28Day = WorkScheduleRepository::getTotalWorkScheduleDoctorByDay($doctorId, 28);
            $totalWorkSchedule7Day = WorkScheduleRepository::getTotalWorkScheduleDoctorByDay($doctorId, 7);
            $totalWorkSchedule1Day = WorkScheduleRepository::getTotalWorkScheduleDoctorByDay($doctorId, 1);
            $statistical = [
                'totalWorkSchedule' => $totalWorkSchedule,
                'totalWorkSchedule28Day' => $totalWorkSchedule28Day,
                'totalWorkSchedule7Day' => $totalWorkSchedule7Day,
                'totalWorkSchedule1Day' => $totalWorkSchedule1Day,
            ];
            if ($statistical) {
                return $this->responseOK(200, $statistical, 'Xem thống kê doctor thành công !');
            }
        } catch (Throwable $e) {
            return $this->responseError(400, $e->getMessage());
        }
    }
}
