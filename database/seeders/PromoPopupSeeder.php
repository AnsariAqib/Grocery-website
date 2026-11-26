<?php

namespace Database\Seeders;

use App\Models\PromoPopup;
use Database\Seeders\Concerns\CopiesSeedMedia;
use Illuminate\Database\Seeder;

class PromoPopupSeeder extends Seeder
{
    use CopiesSeedMedia;

    /**
     * Three popups covering the states the admin will actually work in: one
     * running now, one waiting for its start date, and one that has already
     * finished. Between them the Promo Popups list shows every status badge
     * without anyone having to build test rows by hand.
     *
     * Only the first is live, and only one popup ever opens at a time, so a
     * fresh install shows exactly one interstitial.
     */
    public function run(): void
    {
        $popups = [
            [
                'title' => 'Weekly ration offer',
                'eyebrow' => 'LIMITED TIME',
                'headline' => 'Flat 15% off your monthly ration',
                'body' => 'On every grocery combo above ₹2,999 at Vinayak Stores. Walk in to either store or send us your list and we will keep it ready.',
                'cta_text' => 'See all offers',
                'cta_url' => '/promotions',
                'cta_opens_enquiry' => false,
                'image' => '/assets/promo-combo.png',
                'audience' => 'all',
                'pages' => null,
                'frequency' => 'once_per_day',
                'delay_seconds' => 4,
                'sort_order' => 0,
                'is_active' => true,
                // Open-ended: this is the one a fresh install should show.
                'publish_at' => null,
                'expire_at' => null,
            ],
            [
                'title' => 'Festive season teaser',
                'eyebrow' => 'COMING SOON',
                'headline' => 'Our festive gift range opens next week',
                'body' => 'Sweets, dry fruit boxes and corporate gift hampers, priced for bulk. Register your requirement early and we will hold your stock.',
                'cta_text' => 'Register interest',
                'cta_url' => null,
                'cta_opens_enquiry' => true,
                'image' => '/assets/promo-festive.png',
                'audience' => 'all',
                'pages' => null,
                'frequency' => 'once_per_session',
                'delay_seconds' => 5,
                'sort_order' => 1,
                'is_active' => true,
                // Starts a week out, so the list has a "Scheduled" row to look at.
                'publish_at' => now()->addWeek()->startOfDay(),
                'expire_at' => now()->addWeeks(4)->endOfDay(),
            ],
            [
                'title' => 'B2B bulk supply drive (ended)',
                'eyebrow' => 'B2B WHOLESALE',
                'headline' => 'GST credit on canteen & pantry bulk orders',
                'body' => 'Direct wholesale invoices with express delivery across Surat, for offices, hotels and institutional kitchens.',
                'cta_text' => 'Get a bulk quote',
                'cta_url' => null,
                'cta_opens_enquiry' => true,
                'image' => '/assets/b2b-products.png',
                // Scoped, so the "Pages" column has something other than
                // "Every page" in it.
                'audience' => 'selected',
                'pages' => ['Promotions', 'B2bCategories'],
                'frequency' => 'once_per_visit',
                'delay_seconds' => 3,
                'sort_order' => 2,
                'is_active' => true,
                // Already over: proves the schedule retires a popup on its own.
                'publish_at' => now()->subMonth()->startOfDay(),
                'expire_at' => now()->subWeek()->endOfDay(),
            ],
        ];

        foreach ($popups as $popup) {
            $popup['image'] = $this->seedMedia($popup['image'], 'popups');

            PromoPopup::updateOrCreate(
                ['title' => $popup['title']],
                $popup,
            );
        }
    }
}
