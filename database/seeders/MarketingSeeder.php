<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Logo;
use App\Models\Promotion;
use Database\Seeders\Concerns\CopiesSeedMedia;
use Illuminate\Database\Seeder;

class MarketingSeeder extends Seeder
{
    use CopiesSeedMedia;

    public function run(): void
    {
        $this->seedHomePromotions();
        $this->seedPagePromotions();
        $this->seedLogos();
        $this->seedCertificates();
    }

    /**
     * The four cards in the home "Promotion" section. They are home-only: the
     * offers page has its own set, so show_on_page stays false.
     */
    private function seedHomePromotions(): void
    {
        $promotions = [
            [
                'badge' => 'WEEKLY SAVINGS',
                'badge_bg' => '#E4652E',
                'title' => 'Supermarket Ration Package',
                'description' => 'Flat 15% OFF on monthly grocery combos above ₹2,999.',
                'cta_text' => 'Claim Offer',
                'image' => '/assets/promo-combo.png',
            ],
            [
                'badge' => 'B2B CORPORATE DEAL',
                'badge_bg' => '#061427',
                'title' => 'Canteen & Pantry Bulk Supply',
                'description' => 'Extra 5% wholesale GST credit benefit for corporate clients.',
                'cta_text' => 'Get B2B Quote',
                'image' => '/assets/promo-weekly.png',
            ],
            [
                'badge' => 'SUPER STORE SPECIAL',
                'badge_bg' => '#38BDF8',
                'title' => 'Housekeeping & Hygiene Packs',
                'description' => 'Sanitizers, floor cleaners & detergents at wholesale prices.',
                'cta_text' => 'Explore Deals',
                'image' => '/assets/promo-festive.png',
            ],
            [
                'badge' => 'SEASONAL PROMO',
                'badge_bg' => '#061427',
                'title' => 'Office & Institutional Supplies',
                'description' => 'Stationery, paper rolls & canteen beverages bulk dispatches.',
                'cta_text' => 'Download List',
                'image' => '/assets/promo-summer.png',
            ],
        ];

        foreach ($promotions as $i => $promotion) {
            $promotion['image'] = $this->seedMedia($promotion['image'], 'promotions');

            Promotion::updateOrCreate(
                ['title' => $promotion['title']],
                $promotion + [
                    'show_on_home' => true,
                    'show_on_page' => false,
                    'sort_order' => $i,
                ],
            );
        }
    }

    /**
     * The twelve cards on /promotions, three per group. Seeded from the page
     * markup rather than PromotionsController's array: the page never used that
     * prop, so the array was dead data that did not match what was rendered.
     *
     * The seventh value is how many months the offer runs for, and it drives
     * both expire_at and the printed "Valid till" line so the two can never
     * disagree. It is relative to seed time on purpose — the dates used to be
     * hardcoded, which meant a seed run after those dates produced offers whose
     * card said "Valid till 31 May 2026" while nothing ever retired them.
     * `null` means an evergreen offer with its own validity wording.
     */
    private function seedPagePromotions(): void
    {
        $promotions = [
            // ---- Latest offers -------------------------------------------
            ['latest', 'MEGA OFFER', '#E4652E', 'MEGA SAVINGS', 'UP TO 20% OFF', 'On Grocery & Daily Essentials', 2, '/assets/promo-combo.png', 'linear-gradient(135deg, #FEF7F3 0%, #FFEFE6 100%)', '#F3D2BF', null, false],
            ['latest', 'MONSOON SPECIAL', '#16A34A', 'HYGIENE & CARE', 'UP TO 10% OFF', 'On Home & Kitchen Cleaning', 3, '/assets/promo-summer.png', 'linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%)', '#BBF7D0', null, false],
            ['latest', 'HEALTH & WELLNESS', '#0891B2', 'PERSONAL CARE', 'UP TO 20% OFF', 'On Health & Skincare Range', 2, 'https://cdn-icons-png.flaticon.com/512/3140/3140306.png', 'linear-gradient(135deg, #ECFEFF 0%, #CFFAFE 100%)', '#A5F3FC', null, false],

            // ---- Festive offers -----------------------------------------
            ['festive', 'FESTIVE OFFER', '#DC2626', 'CELEBRATION PACK', 'FLAT 15% OFF', 'On Selected Gift Combos', 3, '/assets/promo-festive.png', 'linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%)', '#FDE68A', null, false],
            ['festive', 'WEEKEND SPECIAL', '#E11D48', 'WEEKEND BASKET', 'EXTRA 5% OFF', 'On Orders Above ₹999', null, '/assets/store-categories-products.png', 'linear-gradient(135deg, #FFF1F2 0%, #FFE4E6 100%)', '#FECDD3', null, false],
            ['festive', 'FESTIVE BONANZA', '#B91C1C', 'SWEETS & DRY FRUITS', 'FLAT 20% OFF', 'Premium Festive Dry Fruit Boxes', 4, '/assets/promo-combo.png', 'linear-gradient(135deg, #FEF2F2 0%, #FEE2E2 100%)', '#FCA5A5', null, false],

            // ---- Seasonal sale ------------------------------------------
            ['seasonal', 'SUMMER SALE', '#0284C7', 'SUMMER REFRESH', 'UP TO 25% OFF', 'On Beverages & Cold Drinks', 2, '/assets/promo-weekly.png', 'linear-gradient(135deg, #061427 0%, #0F2544 100%)', '#1E3A8A', '#38BDF8', true],
            ['seasonal', 'BACK TO SCHOOL', '#9333EA', 'STATIONERY KIT', 'UP TO 15% OFF', 'On Stationery & Office Packs', 3, 'https://cdn-icons-png.flaticon.com/512/2965/2965320.png', 'linear-gradient(135deg, #FAF5FF 0%, #F3E8FF 100%)', '#E9D5FF', null, false],
            ['seasonal', 'SUMMER COOLERS', '#0369A1', 'FROZEN & SNACKS', 'UP TO 18% OFF', 'Ice Creams & Ready Foods', 2, '/assets/promo-weekly.png', 'linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%)', '#BAE6FD', null, false],

            // ---- Bulk / B2B offers --------------------------------------
            ['bulk', 'BULK BUY OFFER', '#EA580C', 'SPECIAL WHOLESALE', 'WHOLESALE GST CREDIT', 'For Bulk Orders On All Categories', 6, '/assets/b2b-products.png', 'linear-gradient(135deg, #FFF7ED 0%, #FFEDD5 100%)', '#FED7AA', null, false],
            ['bulk', 'CANTEEN PANTRY', '#061427', 'CORPORATE PANTRY', 'BULK CREDIT TERMS', 'Tea, Coffee & Canteen Food Packs', 6, 'https://cdn-icons-png.flaticon.com/512/2921/2921822.png', 'linear-gradient(135deg, #F1F5F9 0%, #E2E8F0 100%)', '#CBD5E1', null, false],
            ['bulk', 'INSTITUTIONAL PACKS', '#D97706', 'HOUSEKEEPING DRUMS', 'FLAT 30% SAVINGS', 'Hospital & Hotel Sanitation Drums', 6, '/assets/b2b-products.png', 'linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%)', '#F59E0B', null, false],
        ];

        foreach ($promotions as $i => [$group, $badge, $badgeBg, $title, $discount, $subtitle, $runsForMonths, $image, $cardBg, $borderColor, $discountColor, $isDark]) {
            $expiresAt = $runsForMonths === null
                ? null
                : now()->addMonths($runsForMonths)->endOfDay();

            Promotion::updateOrCreate(
                ['title' => $title],
                [
                    'group' => $group,
                    'badge' => $badge,
                    'badge_bg' => $badgeBg,
                    'discount' => $discount,
                    'discount_color' => $discountColor,
                    'subtitle' => $subtitle,
                    // The card prints expire_at when it is set, so this line is
                    // only read by the evergreen offers.
                    'validity' => $expiresAt === null ? 'Every Saturday & Sunday' : 'Valid till '.$expiresAt->format('j M Y'),
                    'expire_at' => $expiresAt,
                    'image' => $this->seedMedia($image, 'promotions'),
                    'card_bg' => $cardBg,
                    'border_color' => $borderColor,
                    'is_dark' => $isDark,
                    'cta_text' => 'Claim Deal',
                    'show_on_home' => false,
                    'show_on_page' => true,
                    'sort_order' => $i,
                ],
            );
        }
    }

    private function seedLogos(): void
    {
        $clients = [
            ['Tata', '/assets/client-tata.png'],
            ['Reliance', '/assets/client-reliance.png'],
            ['Adani', '/assets/client-adani.png'],
            ['AMNS', '/assets/client-amns.png'],
            ['Essar', '/assets/client-essar.png'],
            ['Vedanta', '/assets/client-vedanta.png'],
        ];

        foreach ($clients as $i => [$name, $image]) {
            Logo::updateOrCreate(
                ['type' => Logo::TYPE_CLIENT, 'name' => $name],
                ['image' => $this->seedMedia($image, 'logos'), 'sort_order' => $i],
            );
        }

        $brands = [
            ['Amul', '/assets/brand-amul.png'],
            ['Colgate', '/assets/brand-colgate.png'],
            ['Dabur', '/assets/brand-dabur.png'],
            ['Godrej', '/assets/dealer-godrej.png'],
            ['Parle', '/assets/dealer-parle.png'],
            ['Havells', '/assets/brand-havells.png'],
            ['Philips', '/assets/brand-philips.png'],
            ['P&G', '/assets/brand-pg.png'],
        ];

        foreach ($brands as $i => [$name, $image]) {
            Logo::updateOrCreate(
                ['type' => Logo::TYPE_BRAND, 'name' => $name],
                ['image' => $this->seedMedia($image, 'logos'), 'sort_order' => $i],
            );
        }
    }

    private function seedCertificates(): void
    {
        $certificates = [
            ['Amul', 'AUTHORISED DISTRIBUTOR', 'Certified partner for Amul dairy, ice cream & FMCG range.', 'Gujarat Co-op Milk', '/assets/brand-amul.png'],
            ['Colgate-Palmolive', 'OFFICIAL AGENCY', 'Authorised distributor for Colgate oral hygiene care.', 'Colgate Palmolive', '/assets/brand-colgate.png'],
            ['Dabur India', 'DIRECT DISTRIBUTOR', 'Authorised supplier for Dabur healthcare & consumer products.', 'Dabur India Ltd', '/assets/brand-dabur.png'],
            ['Godrej Consumer Care', 'AUTHORISED DEALERSHIP', 'Authorised stockist for Godrej home & personal care lines.', 'Godrej Consumer', '/assets/dealer-godrej.png'],
            ['Parle Products', 'OFFICIAL AGENCY', 'Authorised distribution hub for Parle confectionery & snacks.', 'Parle Products Ltd', '/assets/dealer-parle.png'],
        ];

        foreach ($certificates as $i => [$brand, $title, $description, $signatory, $logo]) {
            Certificate::updateOrCreate(
                ['brand_name' => $brand],
                [
                    'title' => $title,
                    'description' => $description,
                    'signatory' => $signatory,
                    'logo_image' => $this->seedMedia($logo, 'certificates'),
                    'sort_order' => $i,
                ],
            );
        }
    }
}
