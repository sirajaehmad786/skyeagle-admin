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
            $packages = $this->packages();

            foreach ($packages as $index => $item) {
                $category = Category::query()->firstOrCreate(['name' => $item['category']]);
                $slug = Str::slug($item['package_name']);
                $startDate = Carbon::now()->addDays(15 + $index)->toDateString();
                $endDate = Carbon::parse($startDate)->addDays(4)->toDateString();

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
                    'is_featured' => $item['is_featured'],
                    'is_popular' => $item['is_popular'],
                    'is_trending' => $item['is_trending'],
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

        if (Storage::disk('public')->exists($path)) {
            return $path;
        }

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
            ['day' => 5, 'title' => 'Departure from ' . $city, 'description' => 'Check out from the hotel and proceed for the return journey with trip assistance.'],
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
        return '<ul><li>4 nights hotel accommodation in ' . e($item['destination_city']) . '</li><li>Daily breakfast at the hotel</li><li>Airport or railway station pickup and drop</li><li>Private sightseeing transfers</li><li>Basic travel assistance during the trip</li></ul>';
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
            $this->recoveredLegacyPackages(),
            $this->domesticPackages(),
            $this->internationalPackages(),
        );
    }

    protected function recoveredLegacyPackages(): array
    {
        return array_merge(
            $this->buildPackages('Domestic', [
                ['Jaipur Heritage Forts Attraction', 'Royal fort walk and old city highlights', 'Ahmedabad', 'Jaipur', 'Heritage', 18500, ['#bc6c25', '#dda15e'], [1, 0, 0]],
                ['Goa Beach Leisure Attraction', 'Relaxed beaches, cafes, and sunset viewpoints', 'Mumbai', 'Goa', 'Beach', 22500, ['#0081a7', '#fcbf49'], [1, 0, 0]],
                ['Kerala Backwater Houseboat Attraction', 'Houseboat stay and peaceful backwater sightseeing', 'Kochi', 'Alleppey', 'Nature', 26800, ['#2d6a4f', '#95d5b2'], [1, 0, 0]],
                ['Rann of Kutch White Desert Attraction', 'Salt desert, culture, and evening camp experience', 'Ahmedabad', 'Kutch', 'Desert', 19800, ['#6c757d', '#f8f9fa'], [1, 0, 0]],
                ['Udaipur Lake Palace Attraction', 'Romantic lakes, palaces, and heritage streets', 'Vadodara', 'Udaipur', 'Heritage', 24200, ['#457b9d', '#f1faee'], [1, 0, 0]],
                ['Mysore Palace Attraction', 'Palace visit, gardens, and royal city experience', 'Bengaluru', 'Mysore', 'Cultural', 17500, ['#7209b7', '#f72585'], [1, 0, 0]],
                ['Darjeeling Tea Garden Attraction', 'Tea estates, mountain views, and toy train charm', 'Kolkata', 'Darjeeling', 'Hill Station', 28900, ['#386641', '#a7c957'], [1, 0, 0]],
                ['Agra Taj Mahal Attraction', 'Iconic monument visit with local heritage walk', 'Delhi', 'Agra', 'Heritage', 16500, ['#adb5bd', '#e9ecef'], [1, 0, 0]],
                ['Varanasi Ganga Aarti Attraction', 'Spiritual ghats, temples, and sunrise boat ride', 'Lucknow', 'Varanasi', 'Spiritual', 19200, ['#e76f51', '#f4a261'], [1, 0, 0]],
                ['Andaman Island Attraction', 'Blue water beaches and island sightseeing', 'Chennai', 'Andaman', 'Island', 46500, ['#006d77', '#83c5be'], [1, 0, 0]],
                ['Manali Snow Valley Attraction', 'Snow points, valleys, cafes, and mountain views', 'Delhi', 'Manali', 'Hill Station', 27800, ['#1d3557', '#a8dadc'], [0, 1, 0]],
                ['Shimla Mall Road Attraction', 'Colonial streets, scenic hills, and leisure walks', 'Chandigarh', 'Shimla', 'Hill Station', 21500, ['#355070', '#b56576'], [0, 1, 0]],
                ['Jaisalmer Desert Camp Attraction', 'Dunes, camel safari, and cultural night stay', 'Jodhpur', 'Jaisalmer', 'Desert', 23800, ['#9c6644', '#e6ccb2'], [0, 1, 0]],
                ['Rishikesh Riverfront Attraction', 'River views, yoga corners, and adventure activities', 'Delhi', 'Rishikesh', 'Adventure', 20500, ['#0077b6', '#90e0ef'], [0, 1, 0]],
                ['Ooty Botanical Garden Attraction', 'Tea hills, gardens, lake, and mountain weather', 'Coimbatore', 'Ooty', 'Nature', 22600, ['#2b9348', '#bfd200'], [0, 1, 0]],
                ['Munnar Tea Valley Attraction', 'Green valleys, plantations, and relaxed viewpoints', 'Kochi', 'Munnar', 'Nature', 24800, ['#006400', '#9ef01a'], [0, 1, 0]],
                ['Leh Monastery Attraction', 'Monasteries, landscapes, and high-altitude culture', 'Delhi', 'Leh', 'Adventure', 44500, ['#495057', '#ced4da'], [0, 1, 0]],
                ['Kashmir Garden Attraction', 'Gardens, lakes, and scenic valley experiences', 'Delhi', 'Srinagar', 'Family Vacation', 39200, ['#2a9d8f', '#e9c46a'], [0, 1, 0]],
                ['Hyderabad City Attraction', 'Historic monuments, food trail, and city highlights', 'Pune', 'Hyderabad', 'Cultural', 18400, ['#5f0f40', '#fb8b24'], [0, 1, 0]],
                ['Pondicherry French Quarter Attraction', 'French streets, beaches, cafes, and heritage corners', 'Chennai', 'Pondicherry', 'Beach', 19600, ['#00afb9', '#fed9b7'], [0, 1, 0]],
            ], 'attraction'),
            $this->buildPackages('International', [
                ['Dubai Luxury City Tour', 'Modern skyline, desert safari, shopping, and marina nights', 'Ahmedabad', 'Dubai', 'International', 78500, ['#023047', '#ffb703'], [0, 0, 1]],
            ]),
        );
    }

    protected function domesticPackages(): array
    {
        return $this->buildPackages('Domestic', [
            ['Jaipur Royal Heritage Tour', 'Amber Fort, City Palace, bazaars, and royal Jaipur evenings', 'Ahmedabad', 'Jaipur', 'Heritage', 18500, ['#bc6c25', '#dda15e'], [1, 1, 0]],
            ['Goa Beach Escape Tour', 'North Goa beaches, cafes, nightlife, and relaxed sunset time', 'Mumbai', 'Goa', 'Beach', 22500, ['#0081a7', '#fcbf49'], [1, 0, 1]],
            ['Kerala Backwater Bliss Tour', 'Alleppey houseboat, backwaters, village views, and calm stays', 'Kochi', 'Alleppey', 'Nature', 26800, ['#2d6a4f', '#95d5b2'], [0, 1, 1]],
            ['Rann of Kutch Desert Festival Tour', 'White desert, handicraft villages, cultural night, and camp stay', 'Ahmedabad', 'Kutch', 'Desert', 19800, ['#6c757d', '#f8f9fa'], [1, 0, 0]],
            ['Udaipur Lake Romance Tour', 'Lake Pichola, palaces, old streets, and rooftop dining views', 'Vadodara', 'Udaipur', 'Honeymoon', 24200, ['#457b9d', '#f1faee'], [0, 1, 0]],
            ['Mysore Palace Culture Tour', 'Mysore Palace, Chamundi Hills, gardens, and local food trail', 'Bengaluru', 'Mysore', 'Cultural', 17500, ['#7209b7', '#f72585'], [0, 0, 1]],
            ['Darjeeling Tea Valley Tour', 'Tea estates, Tiger Hill sunrise, toy train charm, and mountain air', 'Kolkata', 'Darjeeling', 'Hill Station', 28900, ['#386641', '#a7c957'], [1, 1, 1]],
            ['Agra Taj Heritage Tour', 'Taj Mahal sunrise, Agra Fort, marble craft, and Mughal heritage', 'Delhi', 'Agra', 'Heritage', 16500, ['#adb5bd', '#e9ecef'], [0, 1, 0]],
            ['Varanasi Spiritual Ganga Tour', 'Ghats, temples, sunrise boat ride, and evening Ganga aarti', 'Lucknow', 'Varanasi', 'Spiritual', 19200, ['#e76f51', '#f4a261'], [1, 0, 1]],
            ['Andaman Island Lagoon Tour', 'Havelock beaches, blue water, island hopping, and leisure time', 'Chennai', 'Andaman', 'Island', 46500, ['#006d77', '#83c5be'], [0, 1, 1]],
            ['Manali Snow Valley Tour', 'Snow points, valleys, mountain cafes, and riverside leisure', 'Delhi', 'Manali', 'Adventure', 27800, ['#1d3557', '#a8dadc'], [1, 1, 0]],
            ['Jaisalmer Desert Camp Tour', 'Golden fort, dunes, camel safari, and cultural desert night', 'Jodhpur', 'Jaisalmer', 'Desert', 23800, ['#9c6644', '#e6ccb2'], [0, 0, 1]],
            ['Munnar Tea Garden Tour', 'Tea plantations, waterfalls, viewpoints, and cool hill weather', 'Kochi', 'Munnar', 'Nature', 24800, ['#006400', '#9ef01a'], [1, 0, 0]],
            ['Leh Ladakh Monastery Tour', 'Monasteries, high passes, dramatic valleys, and local culture', 'Delhi', 'Leh', 'Adventure', 44500, ['#495057', '#ced4da'], [0, 1, 1]],
            ['Kashmir Valley Garden Tour', 'Srinagar gardens, Dal Lake, scenic valleys, and family leisure', 'Delhi', 'Srinagar', 'Family Vacation', 39200, ['#2a9d8f', '#e9c46a'], [1, 1, 1]],
        ]);
    }

    protected function internationalPackages(): array
    {
        return $this->buildPackages('International', [
            ['Dubai Luxury Skyline Tour', 'Modern skyline, desert safari, shopping, and marina nights', 'Ahmedabad', 'Dubai', 'Luxury', 78500, ['#023047', '#ffb703'], [1, 1, 0]],
            ['Singapore Family Fun Tour', 'Gardens, island attractions, city highlights, and family leisure', 'Mumbai', 'Singapore', 'Family Vacation', 96500, ['#d00000', '#ffba08'], [0, 1, 1]],
            ['Bali Island Honeymoon Tour', 'Beaches, temples, waterfalls, and private couple leisure time', 'Delhi', 'Bali', 'Honeymoon', 88500, ['#007f5f', '#80b918'], [1, 0, 1]],
            ['Thailand Island Explorer Tour', 'Bangkok buzz, Phuket beaches, nightlife, and island activities', 'Mumbai', 'Thailand', 'Island', 72500, ['#ff006e', '#ffbe0b'], [0, 1, 0]],
            ['Maldives Lagoon Escape Tour', 'Water villas, blue lagoons, premium resort stay, and beach calm', 'Kochi', 'Maldives', 'Luxury', 132000, ['#00b4d8', '#caf0f8'], [1, 1, 1]],
            ['Switzerland Alpine Dreams Tour', 'Mountains, lakes, rail rides, and scenic Swiss towns', 'Delhi', 'Switzerland', 'International', 225000, ['#4361ee', '#f8f9fa'], [1, 0, 0]],
            ['Paris Romance City Tour', 'Eiffel views, museums, cafes, and evening city walks', 'Mumbai', 'Paris', 'Honeymoon', 168000, ['#3a0ca3', '#f72585'], [0, 1, 1]],
            ['Japan Cherry Blossom Tour', 'Tokyo, Kyoto, temples, gardens, and seasonal blossom views', 'Delhi', 'Japan', 'Cultural', 198000, ['#ffafcc', '#bde0fe'], [1, 1, 0]],
            ['Vietnam Culture Trail Tour', 'Hanoi streets, Ha Long Bay, lantern towns, and local food trails', 'Kolkata', 'Vietnam', 'Cultural', 84500, ['#606c38', '#dda15e'], [0, 0, 1]],
            ['Turkey Heritage Discovery Tour', 'Istanbul, Cappadocia, bazaars, heritage sites, and scenic valleys', 'Delhi', 'Turkey', 'Heritage', 118000, ['#780000', '#fdf0d5'], [1, 0, 1]],
            ['Greece Island Sunset Tour', 'Athens heritage, Santorini sunsets, blue domes, and island leisure', 'Mumbai', 'Greece', 'Island', 176000, ['#005f73', '#e9d8a6'], [0, 1, 0]],
            ['Australia Coastal Adventure Tour', 'Sydney icons, coastal views, wildlife corners, and city leisure', 'Delhi', 'Australia', 'Adventure', 212000, ['#0a9396', '#ee9b00'], [1, 1, 1]],
            ['Egypt Pyramids Heritage Tour', 'Giza pyramids, Nile moments, museums, and ancient monuments', 'Mumbai', 'Egypt', 'Heritage', 124000, ['#7f5539', '#ddb892'], [0, 1, 1]],
            ['South Africa Safari Tour', 'Cape Town, wildlife safari, coastal drives, and nature experiences', 'Ahmedabad', 'South Africa', 'Wildlife', 184000, ['#283618', '#dda15e'], [1, 0, 0]],
            ['New Zealand Scenic Nature Tour', 'Queenstown views, lakes, valleys, and relaxed outdoor experiences', 'Delhi', 'New Zealand', 'Nature', 236000, ['#264653', '#2a9d8f'], [0, 1, 1]],
        ]);
    }

    protected function buildPackages(string $bookingType, array $items, string $packageType = 'tour'): array
    {
        return array_map(function ($item) use ($bookingType, $packageType) {
            return [
                'booking_type' => $bookingType,
                'package_type' => $packageType,
                'package_name' => $item[0],
                'short_title' => $item[1],
                'source_city' => $item[2],
                'destination_city' => $item[3],
                'category' => $item[4],
                'price' => $item[5],
                'colors' => $item[6],
                'is_featured' => $item[7][0],
                'is_popular' => $item[7][1],
                'is_trending' => $item[7][2],
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'description' => '<p>' . e($item[1]) . ' This package is prepared for travelers who want a well-planned experience with stays, sightseeing, transfers, and enough free time.</p><p>Each day is balanced with guided visits and leisure time so guests can enjoy the destination without feeling rushed.</p>',
            ];
        }, $items);
    }
}
