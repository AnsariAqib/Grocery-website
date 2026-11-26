<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            SettingsSeeder::class,
            CatalogueSeeder::class,   // categories first: the home tiles read from them
            B2bCatalogueSeeder::class,
            HomeContentSeeder::class,
            MarketingSeeder::class,
            PromoPopupSeeder::class,
            StoreSeeder::class,
            LeadershipSeeder::class,
            ShowcaseSeeder::class,
            CategoryBannerSeeder::class,
            TestimonialSeeder::class,
        ]);
    }
}
