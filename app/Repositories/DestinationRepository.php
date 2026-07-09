<?php

namespace App\Repositories;

use App\Models\Destination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DestinationRepository extends BaseRepository
{
    public function __construct(Destination $destination)
    {
        parent::__construct($destination);
    }

    public function initData($request = null)
    {
        $query = Destination::query()->withCount('packages')->latest();

        if ($request) {
            if ($request->filled('status')) {
                $query->where('status', (int) $request->status);
            }

            if ($request->filled('created_from')) {
                $query->where('created_at', '>=', istDateRangeToUtc($request->created_from));
            }

            if ($request->filled('created_to')) {
                $query->where('created_at', '<=', istDateRangeToUtc($request->created_to, true));
            }
        }

        return $query;
    }

    public function createDestination($request): Destination
    {
        $data = $this->payload($request);
        $data['created_by'] = Auth::id();

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $this->uploadBanner($request);
        }

        return $this->model->create($data);
    }

    public function updateDestination($request, int $id): Destination
    {
        $destination = $this->model->findOrFail($id);
        $data = $this->payload($request);

        if ($request->hasFile('banner_image')) {
            if ($destination->banner_image && Storage::disk('public')->exists($destination->banner_image)) {
                Storage::disk('public')->delete($destination->banner_image);
            }
            $data['banner_image'] = $this->uploadBanner($request);
        }

        $destination->update($data);

        return $destination;
    }

    public function deleteDestination(int $id): bool
    {
        $destination = $this->model->findOrFail($id);
        $destination->packages()->update(['destination_id' => null]);

        return (bool) $destination->delete();
    }

    protected function payload($request): array
    {
        return [
            'name' => $request->name,
            'slug' => $request->slug,
            'country' => $request->country,
            'city' => $request->city,
            'description' => $request->description,
            'best_time_to_visit' => $request->best_time_to_visit,
            'popular_attractions' => collect($request->input('popular_attractions', []))
                ->filter(fn ($value) => filled($value))
                ->values()
                ->all(),
            'faqs' => $this->prepareFaqs($request),
            'status' => $request->boolean('status'),
        ];
    }

    protected function prepareFaqs($request): array
    {
        $questions = $request->input('faq_question', []);
        $answers = $request->input('faq_answer', []);
        $faqs = [];

        foreach ($questions as $index => $question) {
            $answer = $answers[$index] ?? null;
            if (blank($question) && blank($answer)) {
                continue;
            }

            $faqs[] = [
                'question' => $question,
                'answer' => $answer,
            ];
        }

        return $faqs;
    }

    protected function uploadBanner($request): string
    {
        $file = $request->file('banner_image');
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs('destinations', $fileName, 'public');
    }
}
