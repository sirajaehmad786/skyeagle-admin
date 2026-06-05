<?php

namespace App\Repositories;

use App\Models\NewsletterSubscriber;

class NewsletterSubscriberRepository extends BaseRepository
{

    public function __construct(NewsletterSubscriber $newsletterSubscriber)
    {
        parent::__construct($newsletterSubscriber);
    }
 
    public function initData()
    {
        return $this->model->latest();
    }
}
