<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\WorkSchedule;
use Illuminate\Support\Facades\DB;
use Throwable;

class WorkScheduleRepository extends BaseRepository implements WorkScheduleInterface
{
    public function getModel()
    {
        return WorkSchedule::class;
    }

    public static function findById($id)
    {
        return (new self)->model->find($id);
    }

    public static function createWorkSchedule($data)
    {
        DB::beginTransaction();
        try {
            $newWorkSchedule = (new self)->model->create($data);
            DB::commit();

            return $newWorkSchedule;
        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public static function getCategory($filter)
    {
        $filter = (object) $filter;
        $data = (new self)->model
            ->when(!empty($filter->id), function ($q) use ($filter) {
                $q->where('id', $filter->id);
            })
            ->when(!empty($filter->list_id), function ($q) use ($filter) {
                $q->whereIn('id', $filter->list_id);
            });

        return $data;
    }

    public static function updateCategory($id, $data)
    {
        DB::beginTransaction();
        try {
            $category = (new self)->model->find($id);
            $category->update($data);
            DB::commit();

            return $category;
        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public static function updateResultCategory($result, $data)
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

    public static function searchCategory($filter)
    {
        $filter = (object) $filter;
        $data = (new self)->model->orderBy($filter->orderBy, $filter->orderDirection)
            ->where('name', 'LIKE', '%' . $filter->search . '%');

        return $data;
    }
}
