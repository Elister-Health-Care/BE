<?php

namespace App\Http\Controllers;

use App\Services\StatisticalService;
use Illuminate\Http\Request;

class StatisticalController extends Controller
{
    protected StatisticalService $statisticalService;

    public function __construct(StatisticalService $statisticalService)
    {
        $this->statisticalService = $statisticalService;
    }

    public function statisticalAccount()
    {
        return $this->statisticalService->getStatisticalAccount();
    }

    public function statisticalHospital($hospitalId)
    {
        return $this->statisticalService->getStatisticalAccountHospital($hospitalId);
    }

    public function statisticalHospitalDoctorByTime(Request $request)
    {
        return $this->statisticalService->getStatisticalHospitalDoctor($request->hospital_id, $request->startDay, $request->endDay);
    }

    public function statisticalHospitalServiceByTime(Request $request)
    {
        return $this->statisticalService->getStatisticalHospital($request->hospital_id, $request->startDay, $request->endDay);
    }

    public function statisticalDoctor($doctorId)
    {
        return $this->statisticalService->getStatisticalDoctor($doctorId);
    }

    public function statisticalAccountByTime(Request $request)
    {
        return $this->statisticalService->getStatisticalAccountByTime($request->startDay, $request->endDay);
    }
}
