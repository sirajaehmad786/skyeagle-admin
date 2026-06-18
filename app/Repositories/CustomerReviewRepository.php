<?php

namespace App\Repositories;

use App\Models\CustomerReview;
use Illuminate\Support\Facades\Storage;

class CustomerReviewRepository extends BaseRepository
{
    public function __construct(CustomerReview $model)
    {
        parent::__construct($model);
    }

    public function saveCustomerReview($request)
    {
        $data = [
            'package_id'          => $request->filled('package_id') ? $request->package_id : null,
            'review_title'       => $request->review_title,
            'review_description' => $request->review_description,
            'reviewer_name'      => $request->reviewer_name,
            'reviewer_location'  => $request->reviewer_location,
            'rating'             => $request->rating ?? 5,
            'sort_order'         => $request->sort_order ?? 1,
        ];
        if ($request->hasFile('reviewer_image')) {
            $image = $request->file('reviewer_image');
            $fileName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
            $path = $image->storeAs(
                'customer-review',
                $fileName,
                'public'
            );
            $data['reviewer_image'] = $path;
        }
        return $this->model->create($data);
    }

    public function initData()
    {
        return $this->model->with('package')->latest();
    }

    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function updateReview($request, $id)
    {
        $review = $this->model->findOrFail($id);
        $data = $request->only([
            'review_title',
            'review_description',
            'rating',
            'sort_order',
            'reviewer_name',
            'reviewer_location',
        ]);
        $data['package_id'] = $request->filled('package_id') ? $request->package_id : null;

        if ($request->hasFile('reviewer_image')) {
            if (
                !empty($review->reviewer_image) &&
                Storage::disk('public')->exists($review->reviewer_image)
            ) {
                Storage::disk('public')->delete($review->reviewer_image);
            }
            $data['reviewer_image'] = $request->file('reviewer_image')->store('customer-review', 'public');
        } elseif ($request->boolean('remove_reviewer_image')) {
            if (
                !empty($review->reviewer_image) &&
                Storage::disk('public')->exists($review->reviewer_image)
            ) {
                Storage::disk('public')->delete($review->reviewer_image);
            }
            $data['reviewer_image'] = null;
        }
        $review->update($data);
        return $review;
    }

    public function deleteReview($id)
    {
        $review = $this->model->findOrFail($id);
        $review->delete();
        return true;
    }
}
