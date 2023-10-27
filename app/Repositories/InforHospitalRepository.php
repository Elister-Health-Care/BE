<?php

namespace App\Repositories;

use App\Models\InforHospital;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class ExampleRepository.
 */
class InforHospitalRepository extends BaseRepository implements InforHospitalInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function getModel()
    {
        return InforHospital::class;
    }

    public static function getInforHospital($filter)
    {
        $filter = (object) $filter;
        $user = (new self)->model
            ->when(!empty($filter->id), function ($q) use ($filter) {
                $q->where('id', $filter->id);
            })
            ->when(!empty($filter->id_hospital), function ($q) use ($filter) {
                $q->where('id_hospital', $filter->id_hospital);
            });

        return $user;
    }

    public static function createHospital($data)
    {
        DB::beginTransaction();
        try {
            $newHospital = (new self)->model->create($data);
            DB::commit();

            return $newHospital;
        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public static function updateInforHospital($id, $data)
    {
        DB::beginTransaction();
        try {
            $inforHospital = (new self)->model->find($id);
            $inforHospital->update($data);
            DB::commit();

            return $inforHospital;
        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public static function updateHospital($result, $data)
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

    public static function searchHospital($filter)
    {
        $filter = (object) $filter;
        $data = (new self)->model->selectRaw('users.*, infor_hospitals.*')
            ->join('users', 'users.id', '=', 'infor_hospitals.id_hospital')
            ->when(!empty($filter->search), function ($q) use ($filter) {
                $q->where(function ($query) use ($filter) {
                    $query->where('users.name', 'LIKE', '%' . $filter->search . '%')
                        ->orWhere('users.address', 'LIKE', '%' . $filter->search . '%')
                        ->orWhere('users.email', 'LIKE', '%' . $filter->search . '%')
                        ->orWhere('users.phone', 'LIKE', '%' . $filter->search . '%')
                        ->orWhere('users.username', 'LIKE', '%' . $filter->search . '%');
                });
            })
            ->when(isset($filter->is_accept), function ($query) use ($filter) {
                if ($filter->is_accept === 'both') {
                } else {
                    $query->where('users.is_accept', $filter->is_accept);
                }
            })
            ->when(isset($filter->province_code), function ($query) use ($filter) {
                $query->where('infor_hospitals.province_code', $filter->province_code);
            })
            ->when(!empty($filter->orderBy), function ($query) use ($filter) {
                $query->orderBy($filter->orderBy, $filter->orderDirection);
            });

        return $data;
    }

    public static function getTotalHospitalAccount()
    {
        return (new self)->model->count();
    }

    public static function formatWeek($input)
    {
        $year = substr($input, 0, 4);
        $week = substr($input, 4);

        return $year . '-' . $week;
    }

    public static function getTotalHospitalAccountByTime($startDay, $endDay)
    {
        $startDay = is_string($startDay) ? new \DateTime($startDay) : $startDay;
        $endDay = is_string($endDay) ? new \DateTime($endDay) : $endDay;

        // $endDay = (new \Carbon\Carbon($endDay))->addDay();
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
