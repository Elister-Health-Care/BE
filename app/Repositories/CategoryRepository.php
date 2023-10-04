<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Throwable;

class CategoryRepository extends BaseRepository implements CategoryInterface
{
    public function getModel()
    {
        return Category::class;
    }

    public static function getCategory($filter)
    {
        $filter = (object) $filter;
        $data = (new self)->model
            ->when(!empty($filter->id), function ($q) use ($filter) {
                $q->where('id', $filter->id);
            });

        return $data;
    }

    public static function createCategory($data)
    {
        DB::beginTransaction();
        try {
            $newCategory = (new self)->model->create($data);
            DB::commit();

            return $newCategory;
        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
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

    public static function searchCategory($filter)
    {
        $data = (new self)->model->orderBy($filter->orderBy, $filter->orderDirection)
            ->where('name', 'LIKE', '%' . $filter->search . '%');

        return $data;
    }
}
