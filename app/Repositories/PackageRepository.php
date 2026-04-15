<?php

namespace App\Repositories;

use App\Models\City;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PackageRepository extends BaseRepository
{
    protected $cityModel;
    public function __construct(Package $package, City $city)
    {
        parent::__construct($package);
        $this->cityModel = $city;
    }

    public function createPackage($request)
    {
        $data = $request->only([ 
            'package_name',
            'source_city_id',
            'destination_city_id',
            'price',
            'min_people',
            'max_people',
            'video_url',
            'description',
            'inclusions',
            'exclusions'
        ]);
        $data['start_date'] = convertDateFormat($request->start_date ?? null);
        $data['end_date']   = convertDateFormat($request->end_date ?? null);
        $data['created_by'] = Auth::id();
        return $this->model->create($data);
    }

    public function initData()
    {
        $packageList = Package::query()->with(['sourceCity', 'destinationCity'])
        ->where('status',1)->select('*')->latest();
        return $packageList;
    }

    public function getById($id)
    {
        return $this->model->with(['sourceCity', 'destinationCity','images'])->findOrFail($id);
    }

    public function updatePackage($request, $id)
    {
        $package = $this->model->findOrFail($id);
        $data = $request->only([ 
            'package_name',
            'source_city_id',
            'destination_city_id',
            'price',
            'min_people',
            'max_people',
            'video_url',
            'description',
            'inclusions',
            'exclusions',
            'start_date',
            'end_date'
        ]);
        $data['start_date'] = Carbon::createFromFormat('d-m-Y', $data['start_date'])->format('Y-m-d');
        $data['end_date'] = Carbon::createFromFormat('d-m-Y', $data['end_date'])->format('Y-m-d');
        return $package->update($data);
    }

    public function uploadPackageImages($request, $packageId)
    {
        if (!$request->hasFile('images')) {
            return;
        }
        $files = $request->file('images');
        if (!is_array($files)) {
            $files = [$files];
        }
        if (count($files) > 10) {
            throw new \Exception('Maximum 10 images allowed');
        }
        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->store('Package Image', 'public');
            DB::table('package_images')->insert([
                'package_id' => $packageId,
                'image' => $path,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function deleteRemovedImages($request)
    {
        if (!$request->removed_images) {
            return;
        }

        $images = json_decode($request->removed_images, true);

        if (!is_array($images)) {
            return;
        }

        foreach ($images as $img) {

            // delete from storage
            if (Storage::disk('public')->exists($img['path'])) {
                Storage::disk('public')->delete($img['path']);
            }

            // delete from DB
            DB::table('package_images')
                ->where('id', $img['id'])
                ->delete();
        }
    }
    public function delete($id)
    {
        $package = Package::findOrFail($id);
        return $package->delete();
    }

    public function getCities()
    {
        return DB::table('cities')
            ->where('flag', 1)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);
    }

    public function searchCities($search = null)
    {
        return $this->cityModel
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name','country_code']);
    }
}
