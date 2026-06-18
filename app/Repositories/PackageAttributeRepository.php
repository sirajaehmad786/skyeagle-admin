<?php

namespace App\Repositories;

use App\Models\PackageAttribute;

class PackageAttributeRepository extends BaseRepository
{
    public function __construct(PackageAttribute $attribute)
    {
        parent::__construct($attribute);
    }

    public function initData($request = null)
    {
        return PackageAttribute::query()->latest();
    }

    public function activeGrouped()
    {
        return PackageAttribute::active()
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('type');
    }

    public function createAttribute($request)
    {
        return $this->model->create([
            'type' => $request->type,
            'name' => $request->name,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->boolean('status') ? 1 : 0,
        ]);
    }

    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function updateAttribute($request, $id)
    {
        $attribute = $this->findById($id);
        $attribute->update([
            'type' => $request->type,
            'name' => $request->name,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->boolean('status') ? 1 : 0,
        ]);

        return $attribute;
    }

    public function delete($id)
    {
        return $this->findById($id)->delete();
    }
}
