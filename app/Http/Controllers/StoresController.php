<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Inertia\Inertia;
use Inertia\Response;

class StoresController extends Controller
{
    /**
     * Display the Our Stores & Other Ventures Page.
     */
    public function index(): Response
    {
        return Inertia::render('Stores', [
            'meta' => [
                'title' => 'Our Stores & Outlets | VIZORA FOODS',
                'description' => 'Locate all 6 Jay Tulsi & Vinayak Stores store locations in Hazira, Icchapore, and Kavas, Surat, Gujarat.',
            ],
            'stores' => Store::active()->ordered()->get()->map(fn (Store $store) => [
                'id' => $store->id,
                'name' => $store->name,
                'type' => $store->type,
                'image' => $store->image_url,
                'gallery' => $store->gallery_urls,
                'address' => $store->address,
                'phone' => $store->phone,
                'phoneLinks' => $store->phone_links ?? [],
                'email' => $store->email,
                'gstNumber' => $store->gst_number,
                // Built in the model so the pin, the pasted Maps link and the
                // address-search fallback all resolve in one place.
                'mapUrl' => $store->map_link,
                'directionsUrl' => $store->directions_link,
                'mapEmbedUrl' => $store->map_embed_link,
            ])->values(),
            'ventures' => [
                [
                    'name' => 'Sathiya Enterprise',
                    'division' => 'Kurti Manufacturing Division',
                    'desc' => 'Premium textile design and ethnic kurti garment manufacturing unit delivering high quality apparel across India.',
                    'icon' => 'fa-shirt',
                    'tag' => 'Textiles & Apparel',
                ],
                [
                    'name' => 'Shubh Avsar Event',
                    'division' => 'Events & Production',
                    'desc' => 'End-to-end corporate event production, grand wedding stage setups, sound & light show management.',
                    'icon' => 'fa-champagne-glasses',
                    'tag' => 'Event Management',
                ],
                [
                    'name' => 'Green Paradise Hospitality Management',
                    'division' => 'Events & Catering',
                    'desc' => 'Enterprise institutional food service, gourmet wedding catering, and luxury hospitality management.',
                    'icon' => 'fa-utensils',
                    'tag' => 'Catering & Hospitality',
                ],
                [
                    'name' => 'Surtaru Wellness Pvt Ltd',
                    'division' => 'Lifestyle & Personal Care',
                    'desc' => 'Holistic wellness products, personal care essentials, and healthy lifestyle solutions.',
                    'icon' => 'fa-spa',
                    'tag' => 'Wellness & Lifestyle',
                ],
            ],
        ]);
    }
}
