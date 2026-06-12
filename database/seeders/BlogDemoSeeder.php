<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogDemoSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->deleteDirectory('blogs/demo');

        $userId = User::query()->value('id');
        $tags = $this->createTags();

        foreach ($this->blogs() as $index => $blog) {
            $category = Category::query()->firstOrCreate(['name' => $blog['category']]);
            $featuredImage = $this->createFeaturedImage($blog, $index);
            $authorImage = $this->createAuthorImage($blog, $index);

            $post = BlogPost::query()->updateOrCreate(
                ['slug' => Str::slug($blog['title'])],
                [
                    'user_id' => $userId,
                    'category_id' => $category->id,
                    'title' => $blog['title'],
                    'excerpt' => $blog['excerpt'],
                    'content' => $blog['content'],
                    'featured_image' => $featuredImage,
                    'author_name' => $blog['author_name'],
                    'author_image' => $authorImage,
                    'author_about' => $blog['author_about'],
                    'status' => 'Active',
                    'is_featured' => $index < 2,
                    'published_at' => Carbon::now('Asia/Kolkata')->subDays($index + 1)->setTimezone('UTC'),
                    'reading_time_minutes' => $blog['reading_time_minutes'],
                    'views_count' => $blog['views_count'],
                ]
            );

            $post->tags()->sync(collect($blog['tags'])->map(fn ($name) => $tags[$name]->id)->all());
            $this->syncImages($post, $blog, $index, $featuredImage);
        }
    }

    protected function createTags(): array
    {
        return collect(['Travel', 'Tips', 'Guide', 'Adventure', 'Hotels', 'Family Travel'])
            ->mapWithKeys(function ($name) {
                $tag = BlogTag::query()->updateOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name, 'status' => 1]
                );

                return [$name => $tag];
            })
            ->all();
    }

    protected function syncImages(BlogPost $post, array $blog, int $index, string $featuredImage): void
    {
        $post->images->each->delete();

        $images = [
            $featuredImage,
            $this->createFeaturedImage($blog, $index, 2),
            $this->createFeaturedImage($blog, $index, 3),
        ];

        foreach ($images as $sortOrder => $image) {
            $post->images()->create([
                'image' => $image,
                'sort_order' => $sortOrder + 1,
            ]);
        }
    }

    protected function createFeaturedImage(array $blog, int $index, int $variant = 1): string
    {
        $path = 'blogs/seed-' . Str::slug($blog['title']) . '-' . $variant . '.png';
        $size = [1200, 650];
        $colors = $blog['colors'];

        $image = imagecreatetruecolor($size[0], $size[1]);
        imageantialias($image, true);
        $start = $this->hexColor($image, $colors[0]);
        $end = $this->hexColor($image, $colors[1]);

        for ($y = 0; $y < $size[1]; $y++) {
            $ratio = $y / $size[1];
            $r = (int) ($start[0] * (1 - $ratio) + $end[0] * $ratio);
            $g = (int) ($start[1] * (1 - $ratio) + $end[1] * $ratio);
            $b = (int) ($start[2] * (1 - $ratio) + $end[2] * $ratio);
            imageline($image, 0, $y, $size[0], $y, imagecolorallocate($image, $r, $g, $b));
        }

        $this->drawTravelScene($image, $blog, $index, $variant);

        $this->storePng($image, $path);

        return $path;
    }

    protected function drawTravelScene($image, array $blog, int $index, int $variant): void
    {
        $white = imagecolorallocate($image, 255, 255, 255);
        $cream = imagecolorallocatealpha($image, 255, 255, 255, 70);
        $dark = imagecolorallocatealpha($image, 0, 0, 0, 68);
        $sun = imagecolorallocatealpha($image, 255, 213, 79, 20);
        $water = imagecolorallocatealpha($image, 63, 167, 214, 25);
        $green = imagecolorallocatealpha($image, 43, 147, 72, 18);
        $sand = imagecolorallocatealpha($image, 244, 196, 121, 20);
        $mountain = imagecolorallocatealpha($image, 52, 73, 94, 18);
        $building = imagecolorallocatealpha($image, 245, 245, 245, 32);

        imagefilledellipse($image, 920 - ($variant * 35), 120 + (($index % 4) * 16), 150, 150, $sun);

        $category = strtolower($blog['category']);
        if (Str::contains($category, ['beach', 'island', 'honeymoon', 'luxury'])) {
            imagefilledrectangle($image, 0, 360, 1200, 500, $water);
            imagefilledrectangle($image, 0, 500, 1200, 650, $sand);
            imagefilledellipse($image, 230, 410, 260 + ($variant * 20), 55, $cream);
            imagefilledellipse($image, 760, 455, 340, 60, $cream);
            $this->drawPalm($image, 170 + ($variant * 36), 325, $green, $mountain);
            $this->drawPalm($image, 980 - ($variant * 28), 350, $green, $mountain);
        } elseif (Str::contains($category, ['adventure', 'nature', 'family vacation'])) {
            $this->drawMountains($image, $mountain, $cream, $variant);
            imagefilledrectangle($image, 0, 495, 1200, 650, $green);
            imagefilledellipse($image, 280, 530, 360, 70, $cream);
            imagefilledellipse($image, 835, 555, 430, 80, $cream);
        } elseif (Str::contains($category, ['cultural', 'heritage', 'travel guide', 'international'])) {
            $this->drawSkyline($image, $building, $cream, $variant);
            imagefilledrectangle($image, 0, 505, 1200, 650, imagecolorallocatealpha($image, 39, 55, 77, 28));
        } else {
            imagefilledrectangle($image, 0, 390, 1200, 650, $water);
            imagefilledellipse($image, 260, 420, 380, 85, $cream);
            imagefilledellipse($image, 850, 470, 430, 100, $cream);
            $this->drawSkyline($image, $building, $cream, $variant);
        }

        imagefilledrectangle($image, 0, 430, 1200, 650, $dark);
        imagestring($image, 5, 60, 485, strtoupper($blog['category']), $white);
        imagestring($image, 5, 60, 535, Str::limit($blog['title'], 72, ''), $white);
        imagestring($image, 4, 60, 585, 'Travel story by ' . $blog['author_name'], $white);
    }

    protected function drawMountains($image, int $mountain, int $snow, int $variant): void
    {
        imagefilledpolygon($image, [0, 500, 245, 170 + ($variant * 8), 505, 500], 3, $mountain);
        imagefilledpolygon($image, [330, 510, 610, 135 + ($variant * 12), 910, 510], 3, $mountain);
        imagefilledpolygon($image, [725, 500, 985, 195 - ($variant * 6), 1200, 500], 3, $mountain);
        imagefilledpolygon($image, [210, 220, 245, 170 + ($variant * 8), 282, 220], 3, $snow);
        imagefilledpolygon($image, [560, 195, 610, 135 + ($variant * 12), 660, 195], 3, $snow);
        imagefilledpolygon($image, [940, 250, 985, 195 - ($variant * 6), 1030, 250], 3, $snow);
    }

    protected function drawPalm($image, int $x, int $y, int $leaf, int $trunk): void
    {
        imagefilledrectangle($image, $x - 8, $y, $x + 8, $y + 150, $trunk);
        imagefilledellipse($image, $x - 38, $y + 8, 95, 36, $leaf);
        imagefilledellipse($image, $x + 42, $y + 5, 105, 38, $leaf);
        imagefilledellipse($image, $x, $y - 28, 42, 98, $leaf);
        imagefilledellipse($image, $x - 10, $y + 30, 52, 108, $leaf);
    }

    protected function drawSkyline($image, int $building, int $light, int $variant): void
    {
        $x = 70;
        $heights = [210, 285, 245, 330, 260, 300, 230, 315, 255, 290];
        foreach ($heights as $key => $height) {
            $width = 62 + (($key + $variant) % 3) * 18;
            $top = 500 - $height;
            imagefilledrectangle($image, $x, $top, $x + $width, 500, $building);
            for ($wy = $top + 30; $wy < 485; $wy += 42) {
                imagefilledrectangle($image, $x + 15, $wy, $x + 30, $wy + 16, $light);
            }
            $x += $width + 32;
        }
    }

    protected function createAuthorImage(array $blog, int $index): string
    {
        $path = 'blogs/authors/demo-' . Str::slug($blog['author_name']) . '.png';
        $image = imagecreatetruecolor(420, 420);
        [$bgOne, $bgTwo] = $blog['author_colors'];
        $start = $this->hexColor($image, $bgOne);
        $end = $this->hexColor($image, $bgTwo);

        for ($y = 0; $y < 420; $y++) {
            $ratio = $y / 420;
            $r = (int) ($start[0] * (1 - $ratio) + $end[0] * $ratio);
            $g = (int) ($start[1] * (1 - $ratio) + $end[1] * $ratio);
            $b = (int) ($start[2] * (1 - $ratio) + $end[2] * $ratio);
            imageline($image, 0, $y, 420, $y, imagecolorallocate($image, $r, $g, $b));
        }

        $white = imagecolorallocate($image, 255, 255, 255);
        $soft = imagecolorallocatealpha($image, 255, 255, 255, 80);
        $initials = collect(explode(' ', $blog['author_name']))
            ->filter()
            ->take(2)
            ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');

        imagefilledellipse($image, 210, 150, 160, 160, $soft);
        imagefilledrectangle($image, 130, 245, 290, 360, $soft);
        imagestring($image, 5, 185, 195, $initials, $white);
        imagestring($image, 5, 80, 370, $blog['author_name'], $white);

        $this->storePng($image, $path);

        return $path;
    }

    protected function storePng($image, string $path): void
    {
        Storage::disk('public')->makeDirectory(dirname($path));
        imagepng($image, Storage::disk('public')->path($path));
        imagedestroy($image);
    }

    protected function hexColor($image, string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    protected function blogs(): array
    {
        return [
            [
                'title' => 'Top 10 Travel Tips for First Time International Travelers',
                'category' => 'Travel Guide',
                'excerpt' => 'A simple and practical guide for planning your first international journey with confidence.',
                'content' => '<p>Planning your first international trip feels exciting, but it also brings many small decisions. Start with a valid passport, visa rules, travel insurance, and a clear daily budget.</p><p>Keep scanned copies of your documents, carry a universal adapter, and save offline maps before you fly. A little preparation keeps the journey smooth and lets you enjoy the destination fully.</p>',
                'author_name' => 'Bryan Bradfield',
                'author_about' => 'Bryan Bradfield is a travel writer who helps first-time travelers plan simple, safe, and memorable international trips.',
                'tags' => ['Travel', 'Tips', 'Guide'],
                'reading_time_minutes' => 5,
                'views_count' => 320,
                'colors' => ['#2d6cdf', '#13a085'],
                'author_colors' => ['#1f7a8c', '#f4a261'],
            ],
            [
                'title' => 'Best Beach Destinations for a Relaxing Summer Holiday',
                'category' => 'Beach',
                'excerpt' => 'Explore beach destinations that are perfect for calm stays, scenic sunsets, and easy vacation planning.',
                'content' => '<p>A beach holiday should feel relaxed from the first day. Choose a destination with clean stays, easy transfers, reliable food options, and activities that match your travel style.</p><p>For families, look for gentle beaches and nearby attractions. For couples, sunset points and boutique stays can make the trip feel more personal.</p>',
                'author_name' => 'Emily Carter',
                'author_about' => 'Emily Carter writes about leisure travel, beach holidays, and destination planning for relaxed vacation experiences.',
                'tags' => ['Travel', 'Family Travel', 'Hotels'],
                'reading_time_minutes' => 4,
                'views_count' => 245,
                'colors' => ['#00a7b5', '#f7b267'],
                'author_colors' => ['#3a86ff', '#ffafcc'],
            ],
            [
                'title' => 'How to Plan an Adventure Tour Without Stress',
                'category' => 'Adventure Tour',
                'excerpt' => 'A checklist-based approach for choosing adventure activities, stays, transport, and safety essentials.',
                'content' => '<p>Adventure tours need strong planning because timing, weather, and safety matter more than usual. Confirm activity operators, review inclusions, and keep buffer time between transfers.</p><p>Pack light but smart: trekking shoes, quick-dry clothing, a basic medical kit, and identity proof should always be easy to access.</p>',
                'author_name' => 'Nathan Brooks',
                'author_about' => 'Nathan Brooks specializes in adventure travel planning, outdoor safety, and experience-led tour itineraries.',
                'tags' => ['Adventure', 'Guide', 'Tips'],
                'reading_time_minutes' => 6,
                'views_count' => 410,
                'colors' => ['#2f4858', '#8ac926'],
                'author_colors' => ['#264653', '#e9c46a'],
            ],
            [
                'title' => 'Smart Hotel Booking Tips for Budget Friendly Trips',
                'category' => 'Budget Yatra',
                'excerpt' => 'Learn how to compare hotels, avoid hidden costs, and book stays that match your trip budget.',
                'content' => '<p>Hotel booking is not only about the lowest price. Check location, cancellation rules, meal options, taxes, and transport access before confirming a stay.</p><p>Compare the final payable amount, read recent reviews, and prefer properties with clear room photos and verified amenities.</p>',
                'author_name' => 'Sophia Martin',
                'author_about' => 'Sophia Martin creates practical travel content focused on budget stays, hotel comparisons, and smarter booking decisions.',
                'tags' => ['Hotels', 'Tips', 'Travel'],
                'reading_time_minutes' => 4,
                'views_count' => 188,
                'colors' => ['#735d78', '#f7d1cd'],
                'author_colors' => ['#6d597a', '#eaac8b'],
            ],
            [
                'title' => 'Family Vacation Planning Guide for Smooth Holidays',
                'category' => 'Family Vacation',
                'excerpt' => 'Plan a family holiday with the right destination, stay, food options, and flexible daily itinerary.',
                'content' => '<p>Family vacations work best when the itinerary is balanced. Keep travel days light, add rest time, and choose stays close to major attractions.</p><p>Before booking, check child-friendly facilities, nearby medical support, food flexibility, and easy transport options. These details make the holiday easier for everyone.</p>',
                'author_name' => 'Olivia Bennett',
                'author_about' => 'Olivia Bennett writes family travel guides with a focus on comfort, safety, and easy planning for all age groups.',
                'tags' => ['Family Travel', 'Guide', 'Tips'],
                'reading_time_minutes' => 5,
                'views_count' => 276,
                'colors' => ['#ef476f', '#ffd166'],
                'author_colors' => ['#118ab2', '#06d6a0'],
            ],
            [
                'title' => 'Dubai Travel Guide for First Time Visitors',
                'category' => 'International',
                'excerpt' => 'A useful Dubai guide covering sightseeing, transport, shopping areas, and desert experiences.',
                'content' => '<p>Dubai is easy to explore when your days are planned around nearby attractions. Keep one day for modern landmarks, one for shopping, and one for the desert safari experience.</p><p>Book major attractions in advance, use metro routes where possible, and keep evenings free for marina views, fountain shows, and relaxed dining.</p>',
                'author_name' => 'Aiden Walker',
                'author_about' => 'Aiden Walker writes destination guides for international travelers who want simple planning and premium city experiences.',
                'tags' => ['Travel', 'Guide', 'Tips'],
                'reading_time_minutes' => 6,
                'views_count' => 512,
                'colors' => ['#023047', '#ffb703'],
                'author_colors' => ['#14213d', '#fca311'],
            ],
            [
                'title' => 'Goa Weekend Trip Plan for Beach Lovers',
                'category' => 'Beach',
                'excerpt' => 'A short Goa plan for beaches, cafes, nightlife, water activities, and peaceful sunset points.',
                'content' => '<p>A Goa weekend works best when the itinerary is not overloaded. Pick either North Goa for nightlife and cafes or South Goa for quieter beaches and relaxed stays.</p><p>Keep mornings for beach walks, afternoons for cafes or water activities, and evenings for sunset points. This rhythm keeps the trip fresh without rushing.</p>',
                'author_name' => 'Mia Thompson',
                'author_about' => 'Mia Thompson covers beach vacations, short breaks, and easy itineraries for relaxed travelers.',
                'tags' => ['Travel', 'Tips', 'Hotels'],
                'reading_time_minutes' => 4,
                'views_count' => 374,
                'colors' => ['#00b4d8', '#ffd166'],
                'author_colors' => ['#006d77', '#ffddd2'],
            ],
            [
                'title' => 'Kerala Backwater Travel Experience Guide',
                'category' => 'Nature',
                'excerpt' => 'Discover houseboat stays, calm backwaters, local food, and peaceful village views in Kerala.',
                'content' => '<p>Kerala backwaters are best enjoyed at a slow pace. Choose a clean houseboat, confirm meal inclusions, and keep enough time to enjoy the canals, farms, and village life.</p><p>Carry light clothing, sunscreen, and a camera. The real beauty of the backwaters is in the quiet moments between sightseeing stops.</p>',
                'author_name' => 'Daniel Foster',
                'author_about' => 'Daniel Foster writes about nature travel, slow journeys, and scenic holiday experiences across India.',
                'tags' => ['Travel', 'Guide', 'Family Travel'],
                'reading_time_minutes' => 5,
                'views_count' => 429,
                'colors' => ['#2d6a4f', '#95d5b2'],
                'author_colors' => ['#386641', '#dda15e'],
            ],
            [
                'title' => 'Manali Adventure Travel Checklist',
                'category' => 'Adventure Tour',
                'excerpt' => 'Plan a Manali adventure trip with packing tips, activity planning, weather checks, and safety basics.',
                'content' => '<p>Manali is popular for mountain views, snow points, cafes, and adventure activities. Before booking, check road conditions, weather updates, and activity availability.</p><p>Pack warm layers, comfortable shoes, medicines, and identity proof. Keep at least one flexible day because mountain weather can change quickly.</p>',
                'author_name' => 'Ethan Morris',
                'author_about' => 'Ethan Morris specializes in mountain travel, adventure itineraries, and practical trip checklists.',
                'tags' => ['Adventure', 'Tips', 'Guide'],
                'reading_time_minutes' => 6,
                'views_count' => 491,
                'colors' => ['#1d3557', '#a8dadc'],
                'author_colors' => ['#03045e', '#90e0ef'],
            ],
            [
                'title' => 'Singapore Family Holiday Planning Tips',
                'category' => 'Family Vacation',
                'excerpt' => 'A family-friendly Singapore guide for attractions, transport, food, and easy day planning.',
                'content' => '<p>Singapore is a great family destination because transport is simple and major attractions are well organized. Plan one major attraction per day and keep evenings lighter.</p><p>Book attraction tickets early, carry comfortable walking shoes, and choose hotels close to MRT stations for smooth movement with family members.</p>',
                'author_name' => 'Grace Wilson',
                'author_about' => 'Grace Wilson writes family travel content focused on easy city holidays, child-friendly plans, and comfort-first itineraries.',
                'tags' => ['Family Travel', 'Guide', 'Travel'],
                'reading_time_minutes' => 5,
                'views_count' => 538,
                'colors' => ['#d00000', '#ffba08'],
                'author_colors' => ['#9d0208', '#ffba08'],
            ],
            [
                'title' => 'Bali Honeymoon Guide for Couples',
                'category' => 'Honeymoon',
                'excerpt' => 'A romantic Bali guide with beaches, temples, waterfalls, villas, and peaceful couple experiences.',
                'content' => '<p>Bali is perfect for couples who want a mix of scenic views, culture, beaches, and private leisure time. Split the stay between Ubud and a beach area for a balanced trip.</p><p>Keep time for temples, waterfalls, spa sessions, and sunset dinners. Private transfers make the experience smoother and more comfortable.</p>',
                'author_name' => 'Lily Anderson',
                'author_about' => 'Lily Anderson creates honeymoon and couple travel guides with a focus on comfort, romance, and memorable stays.',
                'tags' => ['Travel', 'Hotels', 'Guide'],
                'reading_time_minutes' => 5,
                'views_count' => 467,
                'colors' => ['#007f5f', '#80b918'],
                'author_colors' => ['#6a4c93', '#ffca3a'],
            ],
            [
                'title' => 'Rajasthan Cultural Tour Planning Guide',
                'category' => 'Cultural',
                'excerpt' => 'Explore forts, palaces, desert stays, food, markets, and heritage routes across Rajasthan.',
                'content' => '<p>Rajasthan needs thoughtful routing because cities are rich in history and travel time can be long. Jaipur, Jodhpur, Jaisalmer, and Udaipur each need enough breathing space.</p><p>Start sightseeing early, keep evenings for markets and food, and choose heritage stays when possible to make the journey feel more local.</p>',
                'author_name' => 'Noah Peterson',
                'author_about' => 'Noah Peterson writes about heritage travel, cultural routes, and destination stories for curious travelers.',
                'tags' => ['Guide', 'Travel', 'Family Travel'],
                'reading_time_minutes' => 7,
                'views_count' => 603,
                'colors' => ['#bc6c25', '#dda15e'],
                'author_colors' => ['#7f4f24', '#f2cc8f'],
            ],
            [
                'title' => 'Thailand Island Hopping Travel Guide',
                'category' => 'Island',
                'excerpt' => 'A practical Thailand island guide for beaches, ferries, activities, nightlife, and relaxed stays.',
                'content' => '<p>Thailand island hopping is easier when ferry timings and hotel locations are planned before arrival. Avoid changing islands every day because transfers take time.</p><p>Mix beach days with snorkeling, local markets, and viewpoint visits. Keep cash handy for small transfers and island cafes.</p>',
                'author_name' => 'Chloe Davis',
                'author_about' => 'Chloe Davis writes about island vacations, tropical escapes, and easy international travel planning.',
                'tags' => ['Travel', 'Adventure', 'Tips'],
                'reading_time_minutes' => 6,
                'views_count' => 444,
                'colors' => ['#ff006e', '#ffbe0b'],
                'author_colors' => ['#8338ec', '#ff006e'],
            ],
            [
                'title' => 'Kashmir Valley Travel Guide for Families',
                'category' => 'Family Vacation',
                'excerpt' => 'Plan Kashmir with gardens, lakes, valleys, houseboats, local food, and comfortable transfers.',
                'content' => '<p>Kashmir is a beautiful family destination when the itinerary includes enough rest time. Srinagar, Gulmarg, Pahalgam, and Sonmarg should be planned with weather in mind.</p><p>Choose stays with heating in colder months, confirm transport for snow routes, and keep daily plans flexible for better comfort.</p>',
                'author_name' => 'Henry Collins',
                'author_about' => 'Henry Collins writes family-friendly destination guides for scenic holidays, hill stations, and relaxed routes.',
                'tags' => ['Family Travel', 'Guide', 'Hotels'],
                'reading_time_minutes' => 5,
                'views_count' => 521,
                'colors' => ['#2a9d8f', '#e9c46a'],
                'author_colors' => ['#264653', '#2a9d8f'],
            ],
            [
                'title' => 'Maldives Luxury Vacation Planning Guide',
                'category' => 'Luxury',
                'excerpt' => 'A Maldives guide for choosing islands, villas, meal plans, transfers, and water activities.',
                'content' => '<p>Maldives planning starts with the resort island. Check transfer type, villa category, meal plan, house reef quality, and activity inclusions before booking.</p><p>Water villas feel special, but beach villas can be more practical for families. Compare the full package price instead of only the room rate.</p>',
                'author_name' => 'Ava Mitchell',
                'author_about' => 'Ava Mitchell writes luxury travel guides focused on premium stays, island resorts, and refined holiday planning.',
                'tags' => ['Hotels', 'Travel', 'Guide'],
                'reading_time_minutes' => 5,
                'views_count' => 589,
                'colors' => ['#00b4d8', '#caf0f8'],
                'author_colors' => ['#118ab2', '#bde0fe'],
            ],
            [
                'title' => 'Leh Ladakh Road Trip Preparation Guide',
                'category' => 'Adventure Tour',
                'excerpt' => 'Prepare for a Leh Ladakh road trip with permits, acclimatization, route planning, and packing tips.',
                'content' => '<p>Leh Ladakh needs careful planning because altitude, road conditions, and weather can affect the trip. Keep the first day light for acclimatization.</p><p>Carry warm layers, medicines, ID proof, power banks, and water. Plan fuel stops and do not rush high-altitude passes.</p>',
                'author_name' => 'Lucas Turner',
                'author_about' => 'Lucas Turner writes adventure travel guides for road trips, mountain routes, and outdoor preparation.',
                'tags' => ['Adventure', 'Guide', 'Tips'],
                'reading_time_minutes' => 7,
                'views_count' => 631,
                'colors' => ['#495057', '#ced4da'],
                'author_colors' => ['#343a40', '#adb5bd'],
            ],
            [
                'title' => 'Japan Cherry Blossom Travel Guide',
                'category' => 'Cultural',
                'excerpt' => 'Plan a Japan spring trip around cherry blossoms, temples, gardens, trains, and city stays.',
                'content' => '<p>Cherry blossom season in Japan is popular, so hotels and trains should be planned early. Tokyo, Kyoto, and Osaka offer a good mix for first-time visitors.</p><p>Check bloom forecasts, keep flexible park visits, and use rail passes smartly if your itinerary includes multiple cities.</p>',
                'author_name' => 'Harper Lewis',
                'author_about' => 'Harper Lewis writes seasonal travel guides, cultural itineraries, and city-based international travel tips.',
                'tags' => ['Travel', 'Guide', 'Tips'],
                'reading_time_minutes' => 6,
                'views_count' => 552,
                'colors' => ['#ffafcc', '#bde0fe'],
                'author_colors' => ['#ff8fab', '#bde0fe'],
            ],
            [
                'title' => 'Vietnam Food and Culture Trail Guide',
                'category' => 'Cultural',
                'excerpt' => 'A Vietnam travel guide for food streets, heritage towns, bays, local markets, and scenic routes.',
                'content' => '<p>Vietnam is best explored through its food, streets, and local culture. Hanoi, Ha Long Bay, Hoi An, and Ho Chi Minh City make a strong first itinerary.</p><p>Try local dishes, walk through night markets, and keep time for slow heritage experiences instead of only sightseeing stops.</p>',
                'author_name' => 'Isabella Moore',
                'author_about' => 'Isabella Moore writes culture and food travel guides for travelers who enjoy local stories and authentic experiences.',
                'tags' => ['Travel', 'Guide', 'Adventure'],
                'reading_time_minutes' => 6,
                'views_count' => 438,
                'colors' => ['#606c38', '#dda15e'],
                'author_colors' => ['#283618', '#bc6c25'],
            ],
            [
                'title' => 'Europe Multi City Trip Planning Tips',
                'category' => 'International',
                'excerpt' => 'Plan a Europe multi-city trip with routes, trains, stays, budget control, and sightseeing balance.',
                'content' => '<p>A Europe multi-city trip should be planned around geography, not only wishlist places. Choose cities that connect well by train or short flights.</p><p>Keep at least two nights per city, book central stays, and reserve key attractions early. A balanced pace makes the trip more enjoyable.</p>',
                'author_name' => 'William Harris',
                'author_about' => 'William Harris creates international travel planning guides for multi-city routes, budgeting, and itinerary structure.',
                'tags' => ['Travel', 'Tips', 'Hotels'],
                'reading_time_minutes' => 7,
                'views_count' => 614,
                'colors' => ['#4361ee', '#f8f9fa'],
                'author_colors' => ['#3a0ca3', '#4cc9f0'],
            ],
            [
                'title' => 'Rishikesh Adventure Weekend Guide',
                'category' => 'Adventure Tour',
                'excerpt' => 'A Rishikesh weekend guide for rafting, cafes, river views, yoga, and easy adventure planning.',
                'content' => '<p>Rishikesh is ideal for a short adventure break. Book rafting with licensed operators, keep dry clothes ready, and avoid overpacking the weekend.</p><p>Balance adventure with peaceful river walks, cafes, and yoga sessions. The destination works well for friends, couples, and small groups.</p>',
                'author_name' => 'Zoe Parker',
                'author_about' => 'Zoe Parker writes short-trip guides, adventure weekend plans, and practical destination tips for active travelers.',
                'tags' => ['Adventure', 'Tips', 'Travel'],
                'reading_time_minutes' => 4,
                'views_count' => 359,
                'colors' => ['#0077b6', '#90e0ef'],
                'author_colors' => ['#0077b6', '#caf0f8'],
            ],
        ];
    }
}
