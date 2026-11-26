<?php

namespace Database\Seeders;

use App\Models\Store;
use Database\Seeders\Concerns\CopiesSeedMedia;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    use CopiesSeedMedia;

    public function run(): void
    {
        $stores = [
            [
                'name' => 'Vinayak Stores',
                'type' => 'Retail Supermarket Chain',
                'image' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=800&q=80',
                'address' => 'GF01, AMNS Shopping Complex, AMNS Township, Hazira, Surat - 394270',
                'phone' => '9099081400 / 9099081409',
                'phone_links' => ['9099081400', '9099081409'],
                'map_query' => 'GF01 AMNS Shopping Complex AMNS Township Hazira Surat 394270',
            ],
            [
                'name' => 'Jay Tulsi Agency',
                'type' => 'B2B Commercial Supply Hub & HQ',
                'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=80',
                'address' => 'FF06, AMNS Shopping Complex, AMNS Township, Hazira, Surat - 394270',
                'phone' => '9879197739 / 9099081417',
                'phone_links' => ['9879197739', '9099081417'],
                'map_query' => 'FF06 AMNS Shopping Complex AMNS Township Hazira Surat 394270',
            ],
            [
                'name' => 'Kalptaru General Store',
                'type' => 'Grocery & Daily Essentials',
                'image' => 'https://images.unsplash.com/photo-1604719312566-8912e9227c6a?auto=format&fit=crop&w=800&q=80',
                'address' => '117, Vishnu Nagar Society, Icchapore-3, Hazira, Surat - 394510',
                'phone' => '9099081408',
                'phone_links' => ['9099081408'],
                'map_query' => '117 Vishnu Nagar Society Icchapore 3 Hazira Surat 394510',
            ],
            [
                'name' => 'Kalptaru Super Market (KAWAS)',
                'type' => 'Modern Supermarket Outlet',
                'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80',
                'address' => 'Shop No-9, Siddhi Complex, Kavas, Surat - 394510',
                'phone' => '9099081418',
                'phone_links' => ['9099081418'],
                'map_query' => 'Shop No 9 Siddhi Complex Kavas Surat 394510',
            ],
            [
                'name' => 'Kalptaru Super Market (NN)',
                'type' => 'Township Retail Outlet',
                'image' => 'https://images.unsplash.com/photo-1588964895597-cfccd6e2dbf9?auto=format&fit=crop&w=800&q=80',
                'address' => 'AMNS Plaza, AMNS Township, Hazira, Surat - 394270',
                'phone' => '9099081417',
                'phone_links' => ['9099081417'],
                'map_query' => 'AMNS Plaza AMNS Township Hazira Surat 394270',
            ],
            [
                'name' => 'Bhikshu Enterprise',
                'type' => 'Wholesale & Retail Trading',
                'image' => 'https://images.unsplash.com/photo-1534723452862-4c874018d66d?auto=format&fit=crop&w=800&q=80',
                'address' => '61-65, Vishnu Nagar Society, Icchapore-3, Hazira, Surat - 394510',
                'phone' => '9099081417',
                'phone_links' => ['9099081417'],
                'map_query' => '61-65 Vishnu Nagar Society Icchapore 3 Hazira Surat 394510',
            ],
        ];

        foreach ($stores as $i => $store) {
            $store['image'] = $this->seedMedia($store['image'], 'stores');

            Store::updateOrCreate(['name' => $store['name']], $store + ['sort_order' => $i]);
        }
    }
}
