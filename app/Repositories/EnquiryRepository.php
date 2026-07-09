<?php

namespace App\Repositories;

use App\Models\Enquiry;


class EnquiryRepository extends BaseRepository
{
    protected $enquiryModel;
    public function __construct(Enquiry $enquiry)
    {
        parent::__construct($enquiry);
        $this->enquiryModel = $enquiry;
    }

    public function initData($request = null)
    {
        $query = Enquiry::query()->latest();

        if ($request) {
            if ($request->filled('source')) {
                $query->where('source', $request->source);
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

    public function sources()
    {
        return Enquiry::query()
            ->whereNotNull('source')
            ->where('source', '!=', '')
            ->distinct()
            ->orderBy('source')
            ->pluck('source');
    }
    
}
