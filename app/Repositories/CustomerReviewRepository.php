<?php

namespace App\Repositories;

use App\Models\CustomerReview;
use Illuminate\Support\Str;

class CustomerReviewRepository extends BaseRepository
{
    public function __construct(CustomerReview $model)
    {
        parent::__construct($model);
    }

    public function saveCustomerReview($request)
    {
        $data = [
            'review_title'         => $request->review_title,
            'review_description'   => $request->review_description,
            'reviewer_name'        => $request->reviewer_name,
            'reviewer_email'       => $request->reviewer_email,
            'reviewer_phone'       => $request->reviewer_phone,
            'reviewer_designation' => $request->reviewer_designation,
            'reviewer_company'     => $request->reviewer_company,
            'reviewer_location'    => $request->reviewer_location,
            'rating'               => $request->rating ?? 5,
            'sort_order'           => $request->sort_order ?? 1,
            'slug'                 => $request->slug ?: Str::slug($request->reviewer_name),
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
        return $this->model->latest();
    }

    public function deleteReview($id)
    {
        $review = $this->model->findOrFail($id);
        $review->delete();
        return true;
    }
}