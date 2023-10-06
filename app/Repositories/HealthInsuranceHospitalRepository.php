<?php

namespace App\Repositories;

use App\Models\Example;
use App\Models\HealthInsurances;

class HealthInsuranceHospitalRepository extends BaseRepository implements HealthInsuranceHospitalInterface
{
    public function getModel()
    {
        return HealthInsuranceHospital::class;
    }

    public function getExamples($filter)
    {
        $data = $this->model
            ->when(!empty($filter->email), function ($q) use ($filter) {
                $q->where('email', '=', "$filter->email");
            })
            ->when(!empty($filter->name), function ($q) use ($filter) {
                $q->where('name', 'like', "%$filter->name%");
            })
            ->when(!empty($filter->start_at), function ($query) use ($filter) {
                $query->whereDate('created_at', '>=', $filter->start_at);
            })
            ->when(!empty($filter->end_at), function ($query) use ($filter) {
                $query->whereDate('created_at', '<=', $filter->end_at);
            });

        return $data;
    }
}
