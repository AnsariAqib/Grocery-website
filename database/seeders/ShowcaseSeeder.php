<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\GalleryImage;
use App\Models\OrderStep;
use App\Models\Product;
use App\Models\UspCard;
use Database\Seeders\Concerns\CopiesSeedMedia;
use Illuminate\Database\Seeder;

class ShowcaseSeeder extends Seeder
{
    use CopiesSeedMedia;

    public function run(): void
    {
        $this->featureCatalogue();
        $this->gallery();
        $this->uspCards();
        $this->orderSteps();
    }

    /**
     * Give the two home page rails something to show out of the box. Only ever
     * turns the flag on for the first few rows, so an admin's own choices in
     * the panel are never cleared by a re-run.
     */
    private function featureCatalogue(): void
    {
        Category::query()->ofType(Category::TYPE_STORE)->orderBy('sort_order')->limit(8)
            ->get()->each(fn (Category $category) => $category->forceFill(['is_featured' => true])->save());

        Product::query()->orderBy('sort_order')->limit(8)
            ->get()->each(fn (Product $product) => $product->forceFill(['is_featured' => true])->save());
    }

    private function gallery(): void
    {
        $shots = [
            [
                'image' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=900&q=80',
                'title' => 'Inside Vinayak Stores',
                'caption' => 'AMNS Shopping Complex, Hazira',
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1604719312566-8912e9227c6a?auto=format&fit=crop&w=900&q=80',
                'title' => 'Daily Essentials Aisle',
                'caption' => 'Fresh stock every morning',
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=900&q=80',
                'title' => 'B2B Supply Warehouse',
                'caption' => 'Bulk orders dispatched across Gujarat',
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=900&q=80',
                'title' => 'Kalptaru Super Market',
                'caption' => 'Modern supermarket experience',
            ],
        ];

        foreach ($shots as $i => $shot) {
            $shot['image'] = $this->seedMedia($shot['image'], 'gallery');

            GalleryImage::updateOrCreate(
                ['title' => $shot['title']],
                $shot + ['sort_order' => $i]
            );
        }
    }

    private function uspCards(): void
    {
        $cards = [
            [
                'scope' => UspCard::SCOPE_BOTH,
                'icon' => 'fa-certificate',
                'title' => '100% Genuine Products',
                'description' => 'Sourced directly from manufacturers and authorised distributors.',
            ],
            [
                'scope' => UspCard::SCOPE_BOTH,
                'icon' => 'fa-truck-fast',
                'title' => 'Reliable Delivery',
                'description' => 'Doorstep delivery across Surat and the Hazira industrial belt.',
            ],
            [
                'scope' => UspCard::SCOPE_STORE,
                'icon' => 'fa-basket-shopping',
                'title' => 'Everything In One Trip',
                'description' => 'Groceries, personal care, home and kitchen under a single roof.',
            ],
            [
                'scope' => UspCard::SCOPE_STORE,
                'icon' => 'fa-tags',
                'title' => 'Everyday Fair Pricing',
                'description' => 'Honest rates on daily essentials, with offers running all month.',
            ],
            [
                'scope' => UspCard::SCOPE_B2B,
                'icon' => 'fa-file-invoice-dollar',
                'title' => 'GST Invoicing & Credit',
                'description' => 'Compliant invoices, corporate credit terms and account management.',
            ],
            [
                'scope' => UspCard::SCOPE_B2B,
                'icon' => 'fa-warehouse',
                'title' => 'Bulk Supply At Scale',
                'description' => 'Canteen, housekeeping and industrial consumables in contract volumes.',
            ],
        ];

        foreach ($cards as $i => $card) {
            UspCard::updateOrCreate(
                ['title' => $card['title']],
                $card + ['sort_order' => $i]
            );
        }
    }

    private function orderSteps(): void
    {
        $steps = [
            [
                'icon' => 'fa-list-check',
                'title' => 'Share Your Requirement',
                'description' => 'Send us your item list on WhatsApp, by email or through the enquiry form.',
            ],
            [
                'icon' => 'fa-file-invoice',
                'title' => 'Receive A Quotation',
                'description' => 'We confirm availability and send a GST quotation with bulk pricing.',
            ],
            [
                'icon' => 'fa-handshake',
                'title' => 'Confirm The Order',
                'description' => 'Approve the quotation and we schedule your dispatch slot.',
            ],
            [
                'icon' => 'fa-boxes-packing',
                'title' => 'Packing & Quality Check',
                'description' => 'Every carton is checked against your list before it leaves the warehouse.',
            ],
            [
                'icon' => 'fa-truck-fast',
                'title' => 'Doorstep Delivery',
                'description' => 'Delivered to your site or store with the invoice and delivery challan.',
            ],
            [
                'icon' => 'fa-headset',
                'title' => 'Ongoing Account Support',
                'description' => 'A dedicated contact for repeat orders, credit terms and monthly supply.',
            ],
        ];

        foreach ($steps as $i => $step) {
            OrderStep::updateOrCreate(
                ['title' => $step['title']],
                $step + ['sort_order' => $i]
            );
        }
    }
}
