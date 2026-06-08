<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Collection;

class DashboardService
{
    protected ?int $filterUserId = null;

    public function __construct(
        private readonly ?User $user
    ) {
    }

    public function setFilterUserId(?int $userId): self
    {
        $this->filterUserId = $userId;
        return $this;
    }

    protected function allowedUserIds(): ?Collection
    {
        if ($this->filterUserId !== null) {
            return collect([$this->filterUserId]);
        }

        return null;
    }

    public function getRecentActivities(int $limit = 10): Collection
    {
        $allowedUserIds = $this->allowedUserIds();

        $query = Activity::with('user')->latest();

        if ($allowedUserIds) {
            $query->whereIn('activities.user_id', $allowedUserIds);
        }

        return $query->limit($limit)->get();
    }
}
