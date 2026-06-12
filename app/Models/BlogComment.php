<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogComment extends Model
{
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'blog_post_id',
        'parent_id',
        'user_id',
        'name',
        'email',
        'message',
        'status',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public static function statusOptions(): array
    {
        return config('constant.blog_comment_status', [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
        ]);
    }

    public function scopeApproved($query)
    {
        return $query
            ->where('status', self::STATUS_APPROVED)
            ->whereNotNull('approved_at');
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED && $this->approved_at !== null;
    }

    public function post()
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }

    public function parent()
    {
        return $this->belongsTo(BlogComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(BlogComment::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
