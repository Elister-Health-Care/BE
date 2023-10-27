<?php

namespace App\Repositories;

use App\Models\InforUser;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class ExampleRepository.
 */
class InforUserRepository extends BaseRepository implements InforUserInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function getModel()
    {
        return InforUser::class;
    }

    public static function getInforUser($filter)
    {
        $filter = (object) $filter;
        $user = (new self)->model
            ->when(!empty($filter->id), function ($q) use ($filter) {
                $q->where('id', $filter->id);
            })
            ->when(!empty($filter->id_user), function ($q) use ($filter) {
                $q->where('id_user', $filter->id_user);
            })
            ->when(!empty($filter->google_id), function ($q) use ($filter) {
                $q->where('google_id', $filter->google_id);
            })
            ->when(!empty($filter->facebook_id), function ($q) use ($filter) {
                $q->where('facebook_id', $filter->facebook_id);
            });

        return $user;
    }

    public static function updateInforUser($id, $data)
    {
        DB::beginTransaction();
        try {
            $inforUser = (new self)->model->find($id);
            $inforUser->update($data);
            DB::commit();

            return $inforUser;
        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public static function createInforUser($data)
    {
        DB::beginTransaction();
        try {
            $newInforUser = (new self)->model->create($data);
            DB::commit();

            return $newInforUser;
        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public static function getTotalUserAccount()
    {
        return (new self)->model->count();
    }

    public static function formatWeek($input)
    {
        $year = substr($input, 0, 4);
        $week = substr($input, 4);

        return $year . '-' . $week;
    }

    public static function getUserAccountByTime($startDay, $endDay)
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
