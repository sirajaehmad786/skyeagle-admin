<?php

namespace App\Repositories;

use App\Models\BlogTag;

class BlogTagRepository extends BaseRepository
{
    public function __construct(BlogTag $blogTag)
    {
        parent::__construct($blogTag);
    }

    public function active()
    {
        return BlogTag::query()->where('status', 1)->orderBy('name')->get();
    }
}
