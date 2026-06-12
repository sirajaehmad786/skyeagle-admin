<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Package;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PackageDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $userId = User::query()->value('id') ?? 1;

            foreach ($this->packages() as $index => $item) {
                $category = Category::query()->firstOrCreate(['name' => $item['category']]);
                $slug = Str::slug($item['package_name']);
                $startDate = Carbon::now()->addDays(15 + $index)->toDateString();
                $endDate = Carbon::parse($startDate)->addDays(7)->toDateString();

                $data = [
                    'categories_id' => $category->id,
                    'package_type' => $item['package_type'],
                    'package_name' => $item['package_name'],
                    'booking_type' => $item['booking_type'],
                    'short_title' => $item['short_title'],
                    'source_city' => $item['source_city'],
                    'destination_city' => $item['destination_city'],
                    'price' => $item['price'],
                    'min_people' => 2,
                    'max_people' => 12,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'video_url' => $item['video_url'],
                    'description' => $item['description'],
                    'inclusions' => $this->inclusions($item),
                    'exclusions' => $this->exclusions(),
                    'is_featured' => $item['group'] === 'featured' ? 1 : 0,
                    'is_popular' => $item['group'] === 'popular' ? 1 : 0,
                    'is_trending' => $item['group'] === 'trending' ? 1 : 0,
                    'status' => 1,
                    'created_by' => $userId,
                ];

                $package = Package::withTrashed()->where('slug', $slug)->first();

                if ($package) {
                    $package->restore();
                    $package->fill($data);
                    $package->slug = $slug;
                    $package->save();
                } else {
                    $package = new Package($data);
                    $package->slug = $slug;
                    $package->save();
                }

                $this->syncPackageDetails($package, $item, $index);
            }
        });
    }

    protected function syncPackageDetails(Package $package, array $item, int $index): void
    {
        DB::table('package_images')->where('package_id', $package->id)->delete();
        DB::table('package_highlights')->where('package_id', $package->id)->delete();
        DB::table('package_itineraries')->where('package_id', $package->id)->delete();
        DB::table('package_faqs')->where('package_id', $package->id)->delete();

        foreach ($this->highlights($item) as $highlight) {
            DB::table('package_highlights')->insert([
                'package_id' => $package->id,
                'highlight' => $highlight,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($this->itinerary($item) as $day) {
            DB::table('package_itineraries')->insert([
                'package_id' => $package->id,
                'day' => $day['day'],
                'title' => $day['title'],
                'description' => $day['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($this->faqs($item) as $faq) {
            DB::table('package_faqs')->insert([
                'package_id' => $package->id,
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($imageIndex = 1; $imageIndex <= 5; $imageIndex++) {
            $image = $this->createPackageImage($item, $index, $imageIndex);

            DB::table('package_images')->insert([
                'package_id' => $package->id,
                'image' => $image,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function createPackageImage(array $item, int $packageIndex, int $imageIndex): string
    {
        $path = 'packages/seed-' . Str::slug($item['package_name']) . '-' . $imageIndex . '.png';
        $width = 1200;
        $height = 760;
        $image = imagecreatetruecolor($width, $height);
        [$startHex, $endHex] = $item['colors'];
        $start = $this->rgb($startHex);
        $end = $this->rgb($endHex);

        for ($y = 0; $y < $height; $y++) {
            $ratio = $y / $height;
            $r = (int) ($start[0] * (1 - $ratio) + $end[0] * $ratio);
            $g = (int) ($start[1] * (1 - $ratio) + $end[1] * $ratio);
            $b = (int) ($start[2] * (1 - $ratio) + $end[2] * $ratio);
            imageline($image, 0, $y, $width, $y, imagecolorallocate($image, $r, $g, $b));
        }

        $white = imagecolorallocate($image, 255, 255, 255);
        $muted = imagecolorallocatealpha($image, 255, 255, 255, 88);
        $shade = imagecolorallocatealpha($image, 0, 0, 0, 72);

        imagefilledrectangle($image, 0, 500, $width, $height, $shade);
        imagefilledellipse($image, 170 + ($imageIndex * 32), 170, 270, 270, $muted);
        imagefilledellipse($image, 940 - ($imageIndex * 24), 255, 360, 360, $muted);
        imagefilledrectangle($image, 80, 415, 360, 450, $muted);
        imagefilledrectangle($image, 80, 460, 520, 485, $muted);
        imagestring($image, 5, 70, 535, strtoupper($item['destination_city']), $white);
        imagestring($image, 5, 70, 585, $item['package_name'], $white);
        imagestring($image, 4, 70, 635, 'Image ' . $imageIndex . ' of 5', $white);

        Storage::disk('public')->makeDirectory('packages');
        imagepng($image, Storage::disk('public')->path($path));
        imagedestroy($image);

        return $path;
    }

    protected function highlights(array $item): array
    {
        return [
            'Guided visit to the best spots in ' . $item['destination_city'],
            'Comfortable stay with daily breakfast included',
            'Private transfers between major sightseeing points',
            'Local experience curated for ' . strtolower($item['category']) . ' travelers',
            'Trip assistance from arrival to departure',
        ];
    }

    protected function itinerary(array $item): array
    {
        $city = $item['destination_city'];

        return [
            ['day' => 1, 'title' => 'Arrival in ' . $city, 'description' => 'Arrive at the destination, complete hotel check-in, and spend the evening at leisure.'],
            ['day' => 2, 'title' => $city . ' city orientation', 'description' => 'Explore important landmarks, local markets, and scenic viewpoints with guided support.'],
            ['day' => 3, 'title' => 'Signature sightseeing tour', 'description' => 'Cover the most popular attractions connected with the theme of this package.'],
            ['day' => 4, 'title' => 'Local culture and food trail', 'description' => 'Enjoy regional cuisine, cultural corners, and relaxed exploration time.'],
            ['day' => 5, 'title' => 'Adventure and experience day', 'description' => 'Join selected activities based on weather, availability, and traveler comfort.'],
            ['day' => 6, 'title' => 'Shopping and leisure time', 'description' => 'Spend the day at a comfortable pace with shopping, cafes, and optional add-on visits.'],
            ['day' => 7, 'title' => 'Hidden gems near ' . $city, 'description' => 'Visit nearby attractions and capture memorable travel moments with enough buffer time.'],
            ['day' => 8, 'title' => 'Departure', 'description' => 'Check out from the hotel and proceed for the return journey with trip assistance.'],
        ];
    }

    protected function faqs(array $item): array
    {
        return [
            ['question' => 'What is included in this package?', 'answer' => 'Hotel stay, breakfast, selected transfers, sightseeing support, and itinerary assistance are included.'],
            ['question' => 'Can this package be customized?', 'answer' => 'Yes, travel dates, hotel category, activities, and pickup points can be customized as per requirement.'],
            ['question' => 'Is this package suitable for families?', 'answer' => 'Yes, the itinerary is balanced and can be adjusted for families, couples, or small groups.'],
            ['question' => 'Are flights included in the package price?', 'answer' => 'Flights are not included by default unless confirmed separately during booking.'],
            ['question' => 'What is the cancellation policy?', 'answer' => 'Cancellation charges depend on hotel, transport, and activity provider rules at the time of cancellation.'],
        ];
    }

    protected function inclusions(array $item): string
    {
        return '<ul><li>7 nights hotel accommodation in ' . e($item['destination_city']) . '</li><li>Daily breakfast at the hotel</li><li>Airport or railway station pickup and drop</li><li>Private sightseeing transfers</li><li>Basic travel assistance during the trip</li></ul>';
    }

    protected function exclusions(): string
    {
        return '<ul><li>Flight or train tickets</li><li>Lunch, dinner, and personal expenses</li><li>Entry tickets unless mentioned</li><li>Travel insurance and visa charges</li><li>Anything not mentioned in inclusions</li></ul>';
    }

    protected function rgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    protected function packages(): array
    {
        return array_merge(
            $this->featuredPackages(),
            $this->popularPackages(),
            $this->trendingPackages(),
        );
    }

    protected function featuredPackages(): array
    {
        return $this->buildPackages('featured', 'Domestic', 'attraction', [
            ['Jaipur Heritage Forts Attraction', 'Royal fort walk and old city highlights', 'Ahmedabad', 'Jaipur', 'Heritage', 18500, ['#bc6c25', '#dda15e']],
            ['Goa Beach Leisure Attraction', 'Relaxed beaches, cafes, and sunset viewpoints', 'Mumbai', 'Goa', 'Beach', 22500, ['#0081a7', '#fcbf49']],
            ['Kerala Backwater Houseboat Attraction', 'Houseboat stay and peaceful backwater sightseeing', 'Kochi', 'Alleppey', 'Nature', 26800, ['#2d6a4f', '#95d5b2']],
            ['Rann of Kutch White Desert Attraction', 'Salt desert, culture, and evening camp experience', 'Ahmedabad', 'Kutch', 'Desert', 19800, ['#6c757d', '#f8f9fa']],
            ['Udaipur Lake Palace Attraction', 'Romantic lakes, palaces, and heritage streets', 'Vadodara', 'Udaipur', 'Heritage', 24200, ['#457b9d', '#f1faee']],
            ['Mysore Palace Attraction', 'Palace visit, gardens, and royal city experience', 'Bengaluru', 'Mysore', 'Cultural', 17500, ['#7209b7', '#f72585']],
            ['Darjeeling Tea Garden Attraction', 'Tea estates, mountain views, and toy train charm', 'Kolkata', 'Darjeeling', 'Hill Station', 28900, ['#386641', '#a7c957']],
            ['Agra Taj Mahal Attraction', 'Iconic monument visit with local heritage walk', 'Delhi', 'Agra', 'Heritage', 16500, ['#adb5bd', '#e9ecef']],
            ['Varanasi Ganga Aarti Attraction', 'Spiritual ghats, temples, and sunrise boat ride', 'Lucknow', 'Varanasi', 'Spiritual', 19200, ['#e76f51', '#f4a261']],
            ['Andaman Island Attraction', 'Blue water beaches and island sightseeing', 'Chennai', 'Andaman', 'Island', 46500, ['#006d77', '#83c5be']],
        ]);
    }

    protected function popularPackages(): array
    {
        return $this->buildPackages('popular', 'Domestic', 'attraction', [
            ['Manali Snow Valley Attraction', 'Snow points, valleys, cafes, and mountain views', 'Delhi', 'Manali', 'Hill Station', 27800, ['#1d3557', '#a8dadc']],
            ['Shimla Mall Road Attraction', 'Colonial streets, scenic hills, and leisure walks', 'Chandigarh', 'Shimla', 'Hill Station', 21500, ['#355070', '#b56576']],
            ['Jaisalmer Desert Camp Attraction', 'Dunes, camel safari, and cultural night stay', 'Jodhpur', 'Jaisalmer', 'Desert', 23800, ['#9c6644', '#e6ccb2']],
            ['Rishikesh Riverfront Attraction', 'River views, yoga corners, and adventure activities', 'Delhi', 'Rishikesh', 'Adventure', 20500, ['#0077b6', '#90e0ef']],
            ['Ooty Botanical Garden Attraction', 'Tea hills, gardens, lake, and mountain weather', 'Coimbatore', 'Ooty', 'Nature', 22600, ['#2b9348', '#bfd200']],
            ['Munnar Tea Valley Attraction', 'Green valleys, plantations, and relaxed viewpoints', 'Kochi', 'Munnar', 'Nature', 24800, ['#006400', '#9ef01a']],
            ['Leh Monastery Attraction', 'Monasteries, landscapes, and high-altitude culture', 'Delhi', 'Leh', 'Adventure', 44500, ['#495057', '#ced4da']],
            ['Kashmir Garden Attraction', 'Gardens, lakes, and scenic valley experiences', 'Delhi', 'Srinagar', 'Family Vacation', 39200, ['#2a9d8f', '#e9c46a']],
            ['Hyderabad City Attraction', 'Historic monuments, food trail, and city highlights', 'Pune', 'Hyderabad', 'Cultural', 18400, ['#5f0f40', '#fb8b24']],
            ['Pondicherry French Quarter Attraction', 'French streets, beaches, cafes, and heritage corners', 'Chennai', 'Pondicherry', 'Beach', 19600, ['#00afb9', '#fed9b7']],
        ]);
    }

    protected function trendingPackages(): array
    {
        return $this->buildPackages('trending', 'International', 'tour', [
            ['Dubai Luxury City Tour', 'Modern skyline, desert safari, shopping, and marina nights', 'Ahmedabad', 'Dubai', 'International', 78500, ['#023047', '#ffb703']],
            ['Singapore Family Fun Tour', 'Universal-style attractions, gardens, and city highlights', 'Mumbai', 'Singapore', 'Family Vacation', 96500, ['#d00000', '#ffba08']],
            ['Bali Island Honeymoon Tour', 'Beaches, temples, waterfalls, and private leisure time', 'Delhi', 'Bali', 'Honeymoon', 88500, ['#007f5f', '#80b918']],
            ['Thailand Island Explorer Tour', 'Bangkok, Phuket, beaches, and nightlife experiences', 'Mumbai', 'Thailand', 'Island', 72500, ['#ff006e', '#ffbe0b']],
            ['Maldives Lagoon Escape Tour', 'Water villas, blue lagoons, and premium island stay', 'Kochi', 'Maldives', 'Luxury', 132000, ['#00b4d8', '#caf0f8']],
            ['Switzerland Alpine Dreams Tour', 'Mountains, lakes, rail rides, and scenic towns', 'Delhi', 'Switzerland', 'International', 225000, ['#4361ee', '#f8f9fa']],
            ['Paris Romance City Tour', 'Eiffel views, museums, cafes, and evening city walks', 'Mumbai', 'Paris', 'Honeymoon', 168000, ['#3a0ca3', '#f72585']],
            ['Japan Cherry Blossom Tour', 'Tokyo, Kyoto, temples, gardens, and seasonal beauty', 'Delhi', 'Japan', 'Cultural', 198000, ['#ffafcc', '#bde0fe']],
            ['Vietnam Culture Trail Tour', 'Hanoi, bays, lantern towns, and local food trails', 'Kolkata', 'Vietnam', 'Cultural', 84500, ['#606c38', '#dda15e']],
            ['Turkey Heritage Discovery Tour', 'Istanbul, Cappadocia, bazaars, and heritage landscapes', 'Delhi', 'Turkey', 'Heritage', 118000, ['#780000', '#fdf0d5']],
        ]);
    }

    protected function buildPackages(string $group, string $bookingType, string $packageType, array $items): array
    {
        return array_map(function ($item) use ($group, $bookingType, $packageType) {
            return [
                'group' => $group,
                'booking_type' => $bookingType,
                'package_type' => $packageType,
                'package_name' => $item[0],
                'short_title' => $item[1],
                'source_city' => $item[2],
                'destination_city' => $item[3],
                'category' => $item[4],
                'price' => $item[5],
                'colors' => $item[6],
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'description' => '<p>' . e($item[1]) . ' This package is prepared for travelers who want a well-planned experience with stays, sightseeing, transfers, and enough free time.</p><p>Each day is balanced with guided visits and leisure time so guests can enjoy the destination without feeling rushed.</p>',
            ];
        }, $items);
    }
}
