<?php

namespace App\Repositories;

use App\Models\NewsletterSubscriber;

class NewsletterSubscriberRepository extends BaseRepository
{

    public function __construct(NewsletterSubscriber $newsletterSubscriber)
    {
        parent::__construct($newsletterSubscriber);
    }
 
    public function initData($request = null)
    {
        $query = $this->model->latest();

        if ($request) {
            if ($request->filled('subscription_status')) {
                if ($request->subscription_status === 'subscribed') {
                    $query->whereNull('unsubscribed_at');
                }

                if ($request->subscription_status === 'unsubscribed') {
                    $query->whereNotNull('unsubscribed_at');
                }
            }

            if ($request->filled('subscribed_from')) {
                $query->where('subscribed_at', '>=', istDateRangeToUtc($request->subscribed_from));
            }

            if ($request->filled('subscribed_to')) {
                $query->where('subscribed_at', '<=', istDateRangeToUtc($request->subscribed_to, true));
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
}
