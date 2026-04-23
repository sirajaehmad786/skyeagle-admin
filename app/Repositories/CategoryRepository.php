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

    public function initData()
    {
        $query = Category::query()->latest();
        return $query;
    }

    public function search($search)
    {
        return Category::query()
            ->where('name', 'LIKE', "%{$search}%")
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get();
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        return $category->delete();
    }
}
