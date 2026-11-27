<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Store;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    /**
     * Contact Us: enquiry form, contact details, every outlet and the
     * business information block.
     */
    public function index(): Response
    {
        $settings = Setting::map();

        return Inertia::render('Contact', [
            'meta' => [
                'title' => 'Contact Us | VIZORA FOODS',
                'description' => 'Talk to Jay Tulsi Agency & Vinayak Stores — retail store enquiries, B2B bulk supply quotations, store addresses and business details in Surat.',
            ],
            'stores' => Store::active()->ordered()->get()->map(fn (Store $store) => [
                'id' => $store->id,
                'name' => $store->name,
                'type' => $store->type,
                'address' => $store->address,
                'phone' => $store->phone,
                'phoneLinks' => $store->phone_links ?? [],
                'email' => $store->email,
                'gstNumber' => $store->gst_number,
                'mapUrl' => $store->map_link,
                'directionsUrl' => $store->directions_link,
            ])->values(),
            // Facts that belong on a contact page but are not part of the
            // shared `site` prop.
            'business' => [
                'legalName' => $settings['business_legal_name'] ?? 'Jay Tulsi Agency',
                'gstNumber' => $settings['business_gst'] ?? null,
                'established' => $settings['business_established'] ?? '1984',
                'serviceArea' => $settings['business_service_area'] ?? 'Surat, Hazira industrial belt & PAN India B2B supply',
                'workingDays' => $settings['business_working_days'] ?? 'Monday to Sunday',
            ],
        ]);
    }
}
