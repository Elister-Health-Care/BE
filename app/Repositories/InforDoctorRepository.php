<?php

namespace App\Repositories;

use App\Models\InforDoctor;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class ExampleRepository.
 */
class InforDoctorRepository extends BaseRepository implements InforDoctorInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function getModel()
    {
        return InforDoctor::class;
    }

    public static function getInforDoctor($filter)
    {
        $filter = (object) $filter;
        $doctor = (new self)->model
            ->when(!empty($filter->id), function ($q) use ($filter) {
                $q->where('id', $filter->id);
            })
            ->when(!empty($filter->id_doctor), function ($q) use ($filter) {
                $q->where('id_doctor', $filter->id_doctor);
            })
            ->when(!empty($filter->id_department), function ($q) use ($filter) {
                $q->where('id_department', $filter->id_department);
            })
            ->when(!empty($filter->id_hospital), function ($q) use ($filter) {
                $q->where('id_hospital', $filter->id_hospital);
            });

        return $doctor;
    }

    public static function createDoctor($data)
    {
        DB::beginTransaction();
        try {
            $newDoctor = (new self)->model->create($data);
            DB::commit();

            return $newDoctor;
        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public static function updateInforDoctor($id, $data)
    {
        DB::beginTransaction();
        try {
            $inforDoctor = (new self)->model->find($id);
            $inforDoctor->update($data);
            DB::commit();

            return $inforDoctor;
        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public static function updateResult($result, $data)
    {
        DB::beginTransaction();
        try {
            $result->update($data);
            DB::commit();

            return $result;
        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public static function getTotalDoctorAccount($hospitalId)
    {
        if ($hospitalId == 0) {
            return (new self)->model->count();
        } else {
            return (new self)->model->where('id_hospital', $hospitalId)->count();
        }
    }

    public static function getIdDoctor($hospitalId)
    {
        return (new self)->model->where('id_hospital', $hospitalId)->pluck('id_doctor');
    }

    public static function formatWeek($input)
    {
        $year = substr($input, 0, 4);
        $week = substr($input, 4);

        return $year . '-' . $week;
    }

    public static function getTotalDoctorAccountByTime($startDay, $endDay)
    {
        $startDay = is_string($startDay) ? new \DateTime($startDay) : $startDay;
        $endDay = is_string($endDay) ? new \DateTime($endDay) : $endDay;

        $endDay = (new \Carbon\Carbon($endDay));
        $endAddDay = clone $endDay;
        $endAddDay->addDays(1);
        $timeDifference = $endDay->diffInDays($startDay);

        $checkWeek = 0;

        // Dựa vào điều kiện để lựa chọn cách nhóm dữ liệu
        if ($timeDifference >= 30 && $timeDifference <= 90) {
            $groupBy = DB::raw('YEARWEEK(created_at, 1)');
            $dateFormat = 'Y-W'; // Định dạng ngày cho tuần
            $checkWeek = 1;
        } elseif ($timeDifference >= 90) {
            $groupBy = DB::raw('DATE_FORMAT(created_at, "%Y-%m")');
            $dateFormat = 'Y-m'; // Định dạng ngày cho tháng
        } else {
            $groupBy = DB::raw('DATE(created_at)');
            $dateFormat = 'Y-m-d'; // Định dạng ngày cho ngày
        }

        $result = (new self)->model
            ->select($groupBy, DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDay, $endAddDay])
            ->groupBy($groupBy)
            ->get();

        // Tạo một mảng kết quả với count mặc định là 0 cho tất cả ngày
        $dateList = [];
        $currentDate = clone $startDay;

        while ($currentDate <= $endDay) {
            $dateList[] = $currentDate->format($dateFormat);
            $currentDate->add(new \DateInterval('P1D'));
        }

        $resultWithMissingDates = [];
        foreach ($dateList as $date) {
            $resultWithMissingDates[$date] = 0;
        }

        // Cập nhật giá trị count cho các ngày có dữ liệu
        if ($checkWeek == 1) {
            foreach ($result as $record) {
                $resultWithMissingDates[self::formatWeek($record->{$groupBy->getValue()})] = $record->count;
            }
        } else {
            foreach ($result as $record) {
                $resultWithMissingDates[$record->{$groupBy->getValue()}] = $record->count;
            }
        }

        return $resultWithMissingDates;
    }
}
