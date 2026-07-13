<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository extends BaseRepository
{
    protected $model;
    public function __construct(Category $category)
    {
        parent::__construct($category);
        $this->model = $category;
    }

    public function createCategory(array $data)
    {
        return $this->model->create([
            'name' => $data['name']
        ]);
    }

    public function find($id)
    {
        return Category::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $category = $this->find($id);
        $category->update($data);
        return $category;
    }

    public function initData($request = null)
    {
        $query = Category::query()->latest();

        if ($request) {
            if ($request->filled('created_from')) {
                $query->where('created_at', '>=', istDateRangeToUtc($request->created_from));
            }

            if ($request->filled('created_to')) {
                $query->where('created_at', '<=', istDateRangeToUtc($request->created_to, true));
            }
        }

        return $query;
    }

    public function search($search, int $page = 1, int $perPage = 20)
    {
        return Category::query()
            ->when($search, fn ($query) => $query->where('name', 'LIKE', "%{$search}%"))
            ->orderBy('name', 'asc')
            ->paginate($perPage, ['id', 'name'], 'page', $page);
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        return $category->delete();
    }
}
