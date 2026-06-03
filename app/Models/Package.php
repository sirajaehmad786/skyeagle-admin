<?php

namespace App\Models;

use App\Services\PackageCodeService;
use App\Services\SlugService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use SoftDeletes;
    protected $fillable = ['package_name','booking_type','categories_id','package_type','short_title','slug','source_city','destination_city','price','min_people','max_people','start_date','end_date','video_url','description','inclusions','exclusions','is_featured','is_popular','is_trending','status','created_by'];
    protected $dates = ['start_date', 'end_date'];
    
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($package) {
            if (!$package->package_code) {
                $packageCodeService = app(PackageCodeService::class);
                $package->package_code = $packageCodeService->generate();
            }
            if ($package->package_name) {
                $slugService = app(SlugService::class);
                $package->slug = $slugService->generate($package->package_name);
            }
        });
        static::deleting(function ($package) {
            $package->images->each(function ($image) {
                $image->delete();
            });
            $package->faqs->each(function ($faq) {
                $faq->delete();
            });
            $package->highlights->each(function ($highlight) {
                $highlight->delete();
            });
            $package->itineraries->each(function ($itinerary) {
                $itinerary->delete();
            });
        });
    }

    public function getDurationAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        // Days (inclusive)
        $days = $start->diffInDays($end) + 1;
        // Nights
        $nights = $days - 1;
        return [
            'days' => $days,
            'nights' => $nights,
            'text' => "{$days} Days / {$nights} Nights"
        ];
    }
    
    public function images()
    {
        return $this->hasMany(PackageImage::class,'package_id');
    }

    public function faqs()
    {
        return $this->hasMany(PackageFaqs::class, 'package_id');
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function highlights()
    {
        return $this->hasMany(PackageHighlight::class, 'package_id');
    }

    public function itineraries()
    {
        return $this->hasMany(PackageItinerary::class, 'package_id')->orderBy('day');
    }
}
