<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * The B2B side of the catalogue: the /b2b-categories page and the "For
 * Businesses" home panel read from these.
 *
 * Split out of CatalogueSeeder so this taxonomy can be re-applied on its own
 * (`db:seed --class=B2bCatalogueSeeder`) without re-running the ~40 product
 * rows and overwriting whatever the admin has edited since.
 *
 * Slugs are the identity here, not names: a couple of these categories predate
 * this taxonomy under a shorter name and carry an uploaded cut-out PNG, so they
 * are matched on their original slug and renamed in place rather than being
 * recreated (which would orphan the artwork). Slugs are internal — nothing in
 * routing or the Inertia props reads them.
 */
class B2bCatalogueSeeder extends Seeder
{
    public function run(): void
    {
        $slugs = [];

        foreach ($this->categories() as $i => $data) {
            $slugs[] = $data['slug'];

            $category = Category::firstOrNew(['slug' => $data['slug']]);

            $category->fill([
                'name' => $data['name'],
                'type' => Category::TYPE_B2B,
                'sort_order' => $i,
            ]);

            // Presentation belongs to the admin once it has been set there, so
            // these are only seeded onto categories that are still missing them.
            $category->icon ??= $data['icon'];
            $category->home_label ??= $data['home_label'];
            $category->icon_color ??= '#061427';
            $category->is_active ??= true;

            $category->save();

            foreach ($data['subcategories'] as $j => $name) {
                Subcategory::updateOrCreate(
                    ['category_id' => $category->id, 'slug' => Str::slug($name)],
                    ['name' => $name, 'sort_order' => $j],
                );
            }
        }

        // Any B2B category outside this taxonomy would otherwise interleave with
        // it and break the order above, so it is parked past the end. is_active
        // is deliberately not touched: whether a stray category stays published
        // is the admin's call, and this seeder must not undo it on a re-run.
        Category::ofType(Category::TYPE_B2B)
            ->whereNotIn('slug', $slugs)
            ->orderBy('sort_order')
            ->get()
            ->each(fn (Category $category, int $i) => $category->update(['sort_order' => 90 + $i]));
    }

    /**
     * @return array<int, array{slug: string, name: string, icon: string, home_label: string, subcategories: array<int, string>}>
     */
    private function categories(): array
    {
        return [
            [
                // Predates this taxonomy as "Food Service & Canteen".
                'slug' => 'food-service-canteen',
                'name' => 'Food Service & Canteen Supplies',
                'icon' => 'fa-bowl-food',
                'home_label' => 'Food Service &<br>Canteen Supplies',
                'subcategories' => [
                    'Grocery & Staples', 'Beverages', 'Disposable Products',
                    'Food Packaging', 'Kitchen Consumables',
                ],
            ],
            [
                'slug' => 'housekeeping-hygiene',
                'name' => 'Housekeeping & Hygiene',
                'icon' => 'fa-spray-can-sparkles',
                'home_label' => 'Housekeeping &<br>Hygiene',
                'subcategories' => [
                    'Cleaning Chemicals', 'Disinfectants & Sanitizers',
                    'Cleaning Tools & Supplies', 'Tissue & Paper Products',
                    'Waste Management',
                ],
            ],
            [
                'slug' => 'office-supplies-stationery',
                'name' => 'Office Supplies & Stationery',
                'icon' => 'fa-print',
                'home_label' => 'Office Supplies &<br>Stationery',
                'subcategories' => [
                    'Paper Products', 'Writing Instruments', 'Filing & Storage',
                    'Printer Consumables', 'Desk Accessories', 'Corporate Gifts',
                ],
            ],
            [
                'slug' => 'office-furniture',
                'name' => 'Office Furniture',
                'icon' => 'fa-chair',
                'home_label' => 'Office<br>Furniture',
                'subcategories' => [
                    'Workstations', 'Office Seating', 'Storage Solutions',
                    'Meeting & Conference Furniture',
                ],
            ],
            [
                'slug' => 'electrical-lighting',
                'name' => 'Electrical & Lighting',
                'icon' => 'fa-bolt',
                'home_label' => 'Electrical &<br>Lighting',
                'subcategories' => [
                    'Electrical Equipment', 'Switches & Accessories', 'Wires & Cables',
                    'LED Lighting', 'Power Backup Solutions',
                ],
            ],
            [
                'slug' => 'it-electronics',
                'name' => 'IT & Electronics',
                'icon' => 'fa-laptop',
                'home_label' => 'IT &<br>Electronics',
                'subcategories' => [
                    'Computers & Laptops', 'Computer Accessories', 'Networking Products',
                    'Data Storage Devices', 'Consumer Electronics', 'Telecom Equipment',
                ],
            ],
            [
                'slug' => 'security-surveillance',
                'name' => 'Security & Surveillance',
                'icon' => 'fa-video',
                'home_label' => 'Security &<br>Surveillance',
                'subcategories' => [
                    'CCTV Systems', 'Access Control Systems', 'Biometric Devices',
                    'Surveillance Accessories',
                ],
            ],
            [
                'slug' => 'hardware-fasteners',
                'name' => 'Hardware & Fasteners',
                'icon' => 'fa-gear',
                'home_label' => 'Hardware &<br>Fasteners',
                'subcategories' => [
                    'Bolts', 'Nuts', 'Screws', 'Rivets', 'Washers',
                    'Anchors & Studs', 'Industrial Fasteners',
                ],
            ],
            [
                'slug' => 'industrial-tools',
                'name' => 'Industrial Tools',
                'icon' => 'fa-screwdriver-wrench',
                'home_label' => 'Industrial<br>Tools',
                'subcategories' => [
                    'Hand Tools', 'Power Tools', 'Pneumatic Tools', 'Tool Accessories',
                ],
            ],
            [
                'slug' => 'pipes-plumbing-sanitary',
                'name' => 'Pipes, Plumbing & Sanitary',
                'icon' => 'fa-faucet',
                'home_label' => 'Pipes, Plumbing<br>& Sanitary',
                'subcategories' => [
                    'Pipes & Tubes', 'Pipe Fittings', 'Plumbing Accessories',
                    'Sanitaryware', 'Sanitary Fittings', 'Water Storage Solutions',
                ],
            ],
            [
                'slug' => 'safety-ppe',
                'name' => 'Safety & PPE',
                'icon' => 'fa-shield-halved',
                'home_label' => 'Safety &<br>PPE',
                'subcategories' => [
                    'Safety Footwear', 'Safety Gloves', 'Fire Safety Equipment',
                    'First Aid', 'Safety Signage & Barriers',
                ],
            ],
            [
                'slug' => 'chemicals',
                'name' => 'Chemicals',
                'icon' => 'fa-flask',
                'home_label' => 'Chemicals',
                'subcategories' => [
                    'Industrial Chemicals', 'Cleaning Chemicals',
                    'Water Treatment Chemicals', 'Metal Surface Treatment Chemicals',
                ],
            ],
            [
                'slug' => 'packaging-storage',
                'name' => 'Packaging & Storage',
                'icon' => 'fa-boxes-packing',
                'home_label' => 'Packaging &<br>Storage',
                'subcategories' => [
                    'Containers & Boxes', 'Crates & Pallets', 'Woven & Non-Woven Bags',
                    'Wrapping Materials', 'Labels & Signage',
                ],
            ],
            [
                'slug' => 'construction-maintenance',
                'name' => 'Construction & Maintenance',
                'icon' => 'fa-helmet-safety',
                'home_label' => 'Construction &<br>Maintenance',
                'subcategories' => [
                    'Adhesives & Sealants', 'Cement & RMC', 'Paints & Coatings',
                    'Electrical Tools & Accessories', 'Thermoplastic Products',
                ],
            ],
        ];
    }
}
