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
            'is_active'          => $request->boolean('is_active'),
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

    public function initData($request = null)
    {
        $query = $this->model
            ->select('customer_reviews.*')
            ->with('package')
            ->latest('customer_reviews.created_at');

        if ($request) {
            if ($request->filled('package_id')) {
                $query->where('package_id', $request->package_id);
            }

            if ($request->filled('rating')) {
                $query->where('rating', $request->rating);
            }

            if ($request->filled('is_active')) {
                $query->where('is_active', (int) $request->is_active);
            }

            if ($request->filled('created_from')) {
                $query->where('customer_reviews.created_at', '>=', istDateRangeToUtc($request->created_from));
            }

            if ($request->filled('created_to')) {
                $query->where('customer_reviews.created_at', '<=', istDateRangeToUtc($request->created_to, true));
            }
        }

        return $query;
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
            'is_active',
            'reviewer_name',
            'reviewer_location',
        ]);
        $data['is_active'] = $request->boolean('is_active');
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
