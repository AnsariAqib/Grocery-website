<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * The four reviews that used to be written into Components/Testimonials.vue,
 * seeded so the section looks the same the moment the panel takes it over.
 */
class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [
            [
                'name' => 'Rajesh Patel',
                'role' => 'Procurement Lead, Facility Mgmt',
                'tag' => 'Corporate',
                'icon' => 'fa-user-tie',
                'quote' => 'Jay Tulsi Agency has been our primary corporate supply partner for over 4 years. Their bulk pricing on housekeeping, canteen & office supplies is unmatched, and express delivery is always on time!',
            ],
            [
                'name' => 'Anjali Sharma',
                'role' => 'Regular Supermarket Shopper',
                'tag' => 'Retail Customer',
                'icon' => 'fa-user-check',
                'quote' => 'Vinayak Stores is our go-to store for weekly groceries. Clean environment, huge variety of daily essentials, and extremely polite staff. Great discount offers on monthly ration packages!',
            ],
            [
                'name' => 'Dr. Sameer Joshi',
                'role' => 'Administrator, Healthcare Institute',
                'tag' => 'Institutional',
                'icon' => 'fa-user-doctor',
                'quote' => 'We source all our hospital & institute sanitization products from Jay Tulsi. Highly professional team, transparent billing, and 100% genuine brand stock direct from FMCG manufacturers.',
            ],
            [
                'name' => 'Vikram Mehta',
                'role' => 'Owner, Retail Chain',
                'tag' => 'Distribution',
                'icon' => 'fa-store',
                'quote' => 'As a retailer sourcing in bulk, their distribution arm is dependable. Stock is always available, rates are competitive, and the team answers the phone every single time.',
            ],
        ];

        foreach ($reviews as $i => $review) {
            Testimonial::updateOrCreate(
                ['name' => $review['name']],
                $review + ['rating' => 5, 'sort_order' => $i, 'is_active' => true],
            );
        }
    }
}
