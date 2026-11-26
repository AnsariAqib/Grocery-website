<?php

namespace App\Http\Controllers;

use App\Models\Leader;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    /**
     * Display the About Us Page.
     */
    public function index(): Response
    {
        return Inertia::render('About', [
            'meta' => [
                'title' => 'About Us | Jay Tulsi Agency & Vinayak Stores Surat',
                'description' => 'Building trust since 1984 — Jay Tulsi Agency, Vinayak Stores and Kalptaru serve 50,000+ retail customers and 1,000+ businesses across Gujarat with groceries, FMCG, housekeeping and industrial supplies.',
            ],

            'reasons' => [
                [
                    'icon' => 'fa-certificate',
                    'title' => '100% Genuine Products',
                    'desc' => "We source directly from leading manufacturers and authorized distributors, ensuring authentic products from India's most trusted FMCG brands.",
                ],
                [
                    'icon' => 'fa-truck-fast',
                    'title' => 'Reliable Delivery Network',
                    'desc' => "Our dedicated logistics team ensures fast, accurate, and doorstep deliveries across Surat's industrial belt and expanding service locations.",
                ],
                [
                    'icon' => 'fa-file-invoice-dollar',
                    'title' => 'Business-Focused Solutions',
                    'desc' => 'From GST-compliant invoicing and corporate credit facilities to dedicated account management, we simplify procurement for businesses of every size.',
                ],
                [
                    'icon' => 'fa-store',
                    'title' => 'One-Stop Supply Partner',
                    'desc' => "Whether you're a family shopping for daily essentials or a large organization managing procurement, we deliver everything through a single trusted channel.",
                ],
            ],

            'coreValues' => [
                'Integrity in every transaction',
                'Customer-first approach',
                'Quality without compromise',
                'Transparent pricing',
                'Fast and dependable service',
                'Building relationships that last',
            ],

            'milestones' => [
                [
                    'year' => '1984',
                    'title' => 'The Beginning',
                    'desc' => 'Started our first grocery store in KRIBHCO Township, laying the foundation of our retail journey.',
                ],
                [
                    'year' => '1987',
                    'title' => 'Growing with Communities',
                    'desc' => 'Opened Kalptaru General Stores at Icchapore, bringing a better neighborhood shopping experience.',
                ],
                [
                    'year' => '1997',
                    'title' => 'The Supermarket Era',
                    'desc' => 'Launched Vinayak Stores in AMNS Township (formerly Nand Niketan), introducing a modern departmental supermarket experience.',
                ],
                [
                    'year' => '2005',
                    'title' => 'Expansion & Diversification',
                    'desc' => 'Established Kalptaru Super Market and expanded into industrial procurement through Jay Tulsi Agency, serving businesses across the Hazira industrial region.',
                ],
                [
                    'year' => '2013',
                    'title' => 'Retail Expansion',
                    'desc' => 'Added a dedicated fashion retail division, further strengthening our presence within the township community.',
                ],
                [
                    'year' => '2021',
                    'title' => 'PAN India B2B Operations',
                    'desc' => "Expanded Jay Tulsi Agency's corporate supply network to serve industries and institutional customers across India.",
                ],
                [
                    'year' => 'Today & Beyond',
                    'title' => 'Looking Ahead',
                    'desc' => "Expanding into emerging industrial zones, strengthening our B2B distribution network, and increasing our retail footprint across Gujarat while continuing to deliver the trust we've built over four decades.",
                    'isFinal' => true,
                ],
            ],

            // Journey video, displayed right below the timeline.
            'journeyVideo' => [
                'embedUrl' => 'https://www.youtube.com/embed/d7rT-4xfqGU',
                'watchUrl' => 'https://www.youtube.com/watch?v=d7rT-4xfqGU',
                'title' => 'Four Decades of Trust',
                'desc' => 'A short look at how a single neighborhood grocery store grew into a trusted retail and B2B supply organization.',
            ],

            // Managed in the admin under Business → Leadership Team.
            'leadership' => Leader::active()->ordered()->get()->map(fn (Leader $leader) => [
                'id' => $leader->id,
                'name' => $leader->name,
                'company' => $leader->company,
                'desc' => $leader->description,
                'photo' => $leader->photo_url,
                'initials' => $leader->initials,
            ])->values(),
        ]);
    }
}
