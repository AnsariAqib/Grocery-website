<?php

namespace Database\Seeders;

use App\Models\CategoryPanel;
use App\Models\HeroSlide;
use App\Models\Stat;
use Database\Seeders\Concerns\CopiesSeedMedia;
use Illuminate\Database\Seeder;

class HomeContentSeeder extends Seeder
{
    use CopiesSeedMedia;

    public function run(): void
    {
        // title_html is rendered with v-html by HeroSlider.vue, so the markup
        // below is intentional. The button labels are NOT — they render as
        // escaped text and get their arrow from the btn_*_has_arrow columns,
        // so any icon markup put in them would show up literally on the page.
        $slides = [
            [
                'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1400&q=80',
                'title_html' => 'Your Trusted Partner in <br><span class="hero-title-gradient shiny-text">Retail & B2B Supply</span> <br>Solutions',
                'subtext' => 'Delivering quality products, trusted brands and reliable service across our stores and business supply network.',
                'btn_primary_text' => 'Explore Store Categories',
                'btn_primary_link' => '#categories',
                'btn_navy_text' => 'Explore B2B Catalogue',
                'btn_navy_link' => '#b2b',
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=1400&q=80',
                'title_html' => 'Premium Supermarket <br><span class="hero-title-gradient shiny-text">Groceries & Daily Staples</span> <br>For Every Home',
                'subtext' => 'Discover 1000+ top-rated household items, fresh produce, and daily essential products at best wholesale prices.',
                'btn_primary_text' => 'Shop Retail Items',
                'btn_primary_link' => '#categories',
                'btn_navy_text' => 'View Store Locations',
                'btn_navy_link' => '#stores',
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1604719312566-8912e9227c6a?auto=format&fit=crop&w=1400&q=80',
                'title_html' => 'Bulk Commercial Supply <br><span class="hero-title-gradient shiny-text">Corporate & Business Solutions</span> <br>Direct To Facility',
                'subtext' => 'Streamline your enterprise procurement with dedicated B2B pricing, flexible credit terms, and fast express dispatch.',
                'btn_primary_text' => 'Explore B2B Catalogue',
                'btn_primary_link' => '#b2b',
                'btn_navy_text' => 'Get Instant Quote',
                'btn_navy_link' => '#contact',
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1588964895597-cfccd6e2dbf9?auto=format&fit=crop&w=1400&q=80',
                'title_html' => 'Authorised Distributors <br><span class="hero-title-gradient shiny-text">Top National Brands</span> <br>& Direct Network',
                'subtext' => 'Partnering with Amul, Colgate, Dabur, Godrej, Parle and leading FMCG manufacturers across Surat & Gujarat.',
                'btn_primary_text' => 'View Authorised Brands',
                'btn_primary_link' => '#dealers',
                'btn_navy_text' => 'Contact Sales Team',
                'btn_navy_link' => '#contact',
            ],
        ];

        foreach ($slides as $i => $slide) {
            $slide['image'] = $this->seedMedia($slide['image'], 'hero');

            HeroSlide::updateOrCreate(
                ['title_html' => $slide['title_html']],
                $slide + ['sort_order' => $i],
            );
        }

        $stats = [
            ['label' => 'Years of Excellence', 'target' => 25, 'suffix' => '+', 'icon' => 'fa-award'],
            ['label' => 'Supermarket Outlets', 'target' => 5, 'suffix' => '+', 'icon' => 'fa-store'],
            ['label' => 'Product Categories', 'target' => 250, 'suffix' => '+', 'icon' => 'fa-boxes-stacked'],
            ['label' => 'Happy Customers', 'target' => 10000, 'suffix' => '+', 'icon' => 'fa-users'],
            ['label' => 'Quality Assured', 'target' => 100, 'suffix' => '%', 'icon' => 'fa-shield-halved'],
        ];

        foreach ($stats as $i => $stat) {
            Stat::updateOrCreate(['label' => $stat['label']], $stat + ['sort_order' => $i]);
        }

        $panels = [
            [
                'type' => 'store',
                'badge_text' => 'FOR HOMES',
                'heading' => 'Store Categories',
                'subheading' => 'Daily essentials for your family',
                'art_image' => '/assets/store-categories-products.png',
                'cta_text' => 'Explore Store Items',
            ],
            [
                'type' => 'b2b',
                'badge_text' => 'FOR BUSINESSES',
                'heading' => 'B2B Commercial Supply',
                'subheading' => 'Bulk supply solutions for companies & institutions',
                'art_image' => '/assets/b2b-products.png',
                'cta_text' => 'Explore B2B Catalogue',
            ],
        ];

        foreach ($panels as $panel) {
            $panel['art_image'] = $this->seedMedia($panel['art_image'], 'panels');

            CategoryPanel::updateOrCreate(['type' => $panel['type']], $panel);
        }
    }
}
