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

    public function initData()
    {
        return Enquiry::query()->latest();
    }
    
}
