<?php

namespace App\Repositories;

use App\Models\City;
use App\Models\Package;
use App\Models\PackageFaqs;
use App\Models\PackageHighlight;
use App\Models\PackageItinerary;
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

            // ================= HIGHLIGHTS =================
        if (!empty($request->highlights)) {
            $highlights = [];

            foreach ($request->highlights as $highlight) {
                if (!empty($highlight)) {
                    $highlights[] = [
                        'package_id' => $package->id,
                        'highlight'  => trim($highlight),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($highlights)) {
                PackageHighlight::insert($highlights); 
            }
        }

        // ================= ITINERARY =================
        if (!empty($request->itinerary)) {
            $itineraries = [];

            foreach ($request->itinerary as $item) {
                if (!empty($item['day'])) {
                    $itineraries[] = [
                        'package_id'  => $package->id,
                        'day'         => (int) $item['day'],
                        'title'       => $item['title'] ?? null,
                        'description' => $item['description'] ?? null,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }
            }
            if (!empty($itineraries)) {
                PackageItinerary::insert($itineraries);
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
        return $this->model->with(['sourceCity', 'destinationCity','images','highlights','itineraries'])->findOrFail($id);
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
        $existingHighlightIds = PackageHighlight::where('package_id', $id)->pluck('id')->toArray();
        $updatedHighlightIds = [];
        if ($request->highlights && is_array($request->highlights)) {
            foreach ($request->highlights as $item) {
                if (empty(trim($item['highlight'] ?? ''))) {
                    continue;
                }
                if (!empty($item['id']) && in_array($item['id'], $existingHighlightIds)) {
                    PackageHighlight::where('id', $item['id'])->update([
                        'highlight' => $item['highlight']
                    ]);
                    $updatedHighlightIds[] = $item['id'];
                } else {
                    $new = PackageHighlight::create([
                        'package_id' => $id,
                        'highlight' => $item['highlight']
                    ]);
                    $updatedHighlightIds[] = $new->id;
                }
            }
        }
        $deleteHighlightIds = array_diff($existingHighlightIds, $updatedHighlightIds);
        if (!empty($deleteHighlightIds)) {
            PackageHighlight::whereIn('id', $deleteHighlightIds)->delete();
        }
        $existingItineraryIds = PackageItinerary::where('package_id', $id)->pluck('id')->toArray();
        $updatedItineraryIds = [];
        if ($request->itinerary && is_array($request->itinerary)) {
            foreach ($request->itinerary as $item) {
                if (empty($item['title']) && empty($item['description'])) {
                    continue;
                }
                if (!empty($item['id']) && in_array($item['id'], $existingItineraryIds)) {
                    PackageItinerary::where('id', $item['id'])->update([
                        'day' => $item['day'] ?? 1,
                        'title' => $item['title'] ?? '',
                        'description' => $item['description'] ?? '',
                    ]);
                    $updatedItineraryIds[] = $item['id'];
                } else {
                    $new = PackageItinerary::create([
                        'package_id' => $id,
                        'day' => $item['day'] ?? 1,
                        'title' => $item['title'] ?? '',
                        'description' => $item['description'] ?? '',
                    ]);
                    $updatedItineraryIds[] = $new->id;
                }
            }
        }
        $deleteItineraryIds = array_diff($existingItineraryIds, $updatedItineraryIds);
        if (!empty($deleteItineraryIds)) {
            PackageItinerary::whereIn('id', $deleteItineraryIds)->delete();
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
