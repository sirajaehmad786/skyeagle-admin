<?php

namespace App\Repositories;

use App\Models\City;
use App\Models\Package;
use App\Models\PackageFaqs;
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
            'short_title',
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
        $data['categories_id'] = $request->category_id;
        $data['start_date'] = convertDateFormat($request->start_date ?? null);
        $data['end_date']   = convertDateFormat($request->end_date ?? null);
        $data['created_by'] = Auth::id();
        $package = $this->model->create($data);
        if ($request->faq_question && $request->faq_answer) {
            $faqs = [];
            foreach ($request->faq_question as $index => $question) {
                if (!empty($question) && !empty($request->faq_answer[$index])) {
                    $faqs[] = [
                        'package_id' => $package->id,
                        'question'   => $question,
                        'answer'     => $request->faq_answer[$index],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            if (!empty($faqs)) {
                PackageFaqs::insert($faqs);
            }
        }
        DB::commit();
        return $package;
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
            'short_title',
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
        $data['categories_id'] = $request->category_id;
        $data['start_date'] = Carbon::createFromFormat('d-m-Y', $data['start_date'])->format('Y-m-d');
        $data['end_date'] = Carbon::createFromFormat('d-m-Y', $data['end_date'])->format('Y-m-d');
        $package->update($data);
        $faqIds = $request->faq_id ?? [];
        $questions = $request->faq_question ?? [];
        $answers = $request->faq_answer ?? [];

        if ($request->deleted_faq_ids) {
            PackageFaqs::whereIn('id', $request->deleted_faq_ids)->delete();
        }

        foreach ($questions as $index => $question) {
            if (empty($question) && empty($answers[$index])) {
                continue;
            }            
            if (!empty($faqIds[$index])) {
                PackageFaqs::where('id', $faqIds[$index])->update([
                    'question' => $question,
                    'answer' => $answers[$index],
                ]);
            } else {
                PackageFaqs::create([
                    'package_id' => $id,
                    'question' => $question,
                    'answer' => $answers[$index],
                ]);
            }
        }
        return true;
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
            if (Storage::disk('public')->exists($img['path'])) {
                Storage::disk('public')->delete($img['path']);
            }
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
