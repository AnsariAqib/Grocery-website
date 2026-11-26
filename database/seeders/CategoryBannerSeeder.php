<?php

namespace Database\Seeders;

use App\Models\CategoryBanner;
use Database\Seeders\Concerns\CopiesSeedMedia;
use Illuminate\Database\Seeder;

class CategoryBannerSeeder extends Seeder
{
    use CopiesSeedMedia;

    public function run(): void
    {
        $banners = [
            [
                'type' => CategoryBanner::TYPE_STORE,
                'badge_text' => 'Retail Catalogue',
                'badge_icon' => 'fa-store',
                'title' => 'Everything Your',
                'highlight' => 'Home Needs',
                'subtitle' => 'Groceries, fresh produce, personal care and home essentials across every aisle of our supermarkets.',
                'cta_text' => 'Browse All Products',
                'cta_url' => '/products',
                'background_image' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=1920&q=80',
                'is_active' => true,
            ],
            [
                'type' => CategoryBanner::TYPE_B2B,
                'badge_text' => 'Corporate Supply',
                'badge_icon' => 'fa-industry',
                'title' => 'Bulk Supply For',
                'highlight' => 'Every Business',
                'subtitle' => 'Canteen, housekeeping, office and industrial consumables — quoted with GST pricing and delivered to your site.',
                'cta_text' => 'Browse All B2B Products',
                'cta_url' => '/products?category=all',
                'background_image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1920&q=80',
                'is_active' => true,
            ],
        ];

        foreach ($banners as $banner) {
            if (! empty($banner['background_image'])) {
                $banner['background_image'] = $this->seedMedia($banner['background_image'], 'category-banners');
            }

            CategoryBanner::updateOrCreate(
                ['type' => $banner['type']],
                $banner
            );
        }
    }
}
