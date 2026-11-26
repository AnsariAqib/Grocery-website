<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Database\Seeders\Concerns\CopiesSeedMedia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Migrates the catalogue that used to live in CategoriesController and
 * ProductsController. Subcategories are the union of both sources: the
 * Categories page listed varieties with no products (Pasta & Noodles), and the
 * products carried varieties missing from that list (Kitchen Appliances, Bags).
 */
class CatalogueSeeder extends Seeder
{
    use CopiesSeedMedia;

    public function run(): void
    {
        foreach ($this->storeCategories() as $i => $data) {
            $subcategories = $data['subcategories'];
            unset($data['subcategories']);

            $data['image'] = $this->seedMedia($data['image'] ?? null, 'categories');

            $category = Category::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                $data + [
                    'type' => Category::TYPE_STORE,
                    'icon_color' => '#E4652E',
                    'sort_order' => $i,
                ],
            );

            foreach ($subcategories as $j => $name) {
                Subcategory::updateOrCreate(
                    ['category_id' => $category->id, 'slug' => Str::slug($name)],
                    ['name' => $name, 'sort_order' => $j],
                );
            }
        }

        $this->seedProducts();
    }

    private function seedProducts(): void
    {
        // [name, category, variety, brand, image, price, packSize, badge, badgeBg]
        $products = [
            // Grocery
            ['Fortune Sunlite Sunflower Oil (5L)', 'Grocery', 'Oil & Ghee', 'Fortune', '/assets/grocery.PNG', '₹675.00', '5 Litre Can', 'SUPERMARKET BESTSELLER', '#E4652E'],
            ['India Gate Basmati Rice Feast Rozzana (5kg)', 'Grocery', 'Rice & Rice Products', 'India Gate', '/assets/grocery.PNG', '₹490.00', '5 Kg Bag', 'PREMIUM QUALITY', '#061427'],
            ['Aashirvaad Shuddh Chakki Atta (10kg)', 'Grocery', 'Flours', 'Aashirvaad', '/assets/grocery.PNG', '₹430.00', '10 Kg Bag', 'TOP CHOICE', '#16A34A'],
            ['Everest Tikhalal Red Chilli Powder (500g)', 'Grocery', 'Masala & Spices', 'Everest', '/assets/grocery.PNG', '₹225.00', '500 Gram Box', 'SPICE ESSENTIAL', '#DC2626'],
            ['Tata Pure Refined Iodized Salt (1kg)', 'Grocery', 'Salt / Sugar / Jaggery', 'Tata', '/assets/grocery.PNG', '₹28.00', '1 Kg Pack', 'DAILY STAPLE', '#0284C7'],
            ['Unpolished Toor Dal / Arhar Dal (1kg)', 'Grocery', 'Dal & Pulses', 'Vinayak Select', '/assets/grocery.PNG', '₹145.00', '1 Kg Pouch', 'PROTEIN RICH', '#D97706'],
            ['Happilo Premium California Almonds (500g)', 'Grocery', 'Dry Fruits', 'Happilo', '/assets/grocery.PNG', '₹475.00', '500g Zipper Pouch', 'HEALTHY NUTS', '#7C3AED'],
            ['Organic Tattva Certified Moong Dal (1kg)', 'Grocery', 'Organic Food', 'Organic Tattva', '/assets/grocery.PNG', '₹195.00', '1 Kg Eco Pack', '100% ORGANIC', '#15803D'],

            // Fruits & Vegetables
            ['Fresh Farm Hybrid Tomatoes (1kg)', 'Fruits & Vegetables', 'Fresh Vegetables', 'Vinayak Fresh', '/assets/fruits and veggies.png', '₹40.00', '1 Kg Pack', 'DAILY FRESH', '#16A34A'],
            ['Premium Shimla Royal Apples (1kg)', 'Fruits & Vegetables', 'Fresh Fruits', 'Vinayak Fresh', '/assets/fruits and veggies.png', '₹180.00', '1 Kg Box', 'ORCHARD FRESH', '#EA580C'],
            ['Fresh Alphonso Mangoes Box (1 Dozen)', 'Fruits & Vegetables', 'Fresh Fruits', 'Ratnagiri Fresh', '/assets/fruits and veggies.png', '₹750.00', '12 Pieces Crate', 'KING OF FRUITS', '#D97706'],
            ['Farm Fresh Green Peas & Potatoes (2kg Combo)', 'Fruits & Vegetables', 'Fresh Vegetables', 'Vinayak Fresh', '/assets/fruits and veggies.png', '₹90.00', '2 Kg Mesh Bag', 'FARM SAVER', '#059669'],

            // Packaged Food
            ['Parle-G Gold Glucose Biscuits (Pack of 4)', 'Packaged Food', 'Biscuits & Cookies', 'Parle', '/assets/promo-weekly.png', '₹120.00', '1 Kg Family Pack', 'POPULAR SNACK', '#D97706'],
            ["Kellogg's Corn Flakes Real Almond Honey (1kg)", 'Packaged Food', 'Breakfast Cereals', "Kellogg's", '/assets/promo-weekly.png', '₹495.00', '1 Kg Carton', 'HEALTHY BREAKFAST', '#0284C7'],
            ['Maggi 2-Minute Masala Noodles (Pack of 12)', 'Packaged Food', 'Instant Food', 'Nestle', '/assets/promo-weekly.png', '₹168.00', '12 x 70g Packs', 'INSTANT FAVORITE', '#E4652E'],
            ['Kissan Mixed Fruit Jam Glass Jar (700g)', 'Packaged Food', 'Jams & Spreads', 'Kissan', '/assets/promo-weekly.png', '₹210.00', '700 Gram Glass Jar', 'KIDS FAVORITE', '#E11D48'],
            ['Heinz Tomato Ketchup Squeezy Bottle (950g)', 'Packaged Food', 'Ketchup & Sauces', 'Heinz', '/assets/promo-weekly.png', '₹175.00', '950g Bottle', 'RICH TASTE', '#DC2626'],
            ['Ferrero Rocher Premium Chocolates Box (24 Pcs)', 'Packaged Food', 'Chocolates & Candies', 'Ferrero Rocher', '/assets/promo-weekly.png', '₹899.00', '300g Gift Box', 'LUXURY GIFT', '#B45309'],

            // Dairy & Beverages
            ['Amul Taaza Toned Fresh Milk (1L Pouch)', 'Dairy & Beverages', 'Dairy', 'Amul', '/assets/milk bevrages.png', '₹56.00', '1 Litre Pouch', 'FRESH DAIRY', '#0284C7'],
            ['Coca-Cola Soft Drink Bottle (2.25L)', 'Dairy & Beverages', 'Beverages', 'Coca-Cola', '/assets/milk bevrages.png', '₹95.00', '2.25 Litre Bottle', 'PARTY SIZE', '#DC2626'],
            ['Britannia White Sandwich Bread (400g)', 'Dairy & Beverages', 'Breads', 'Britannia', '/assets/milk bevrages.png', '₹45.00', '400g Fresh Pack', 'DAILY BAKERY', '#D97706'],
            ['Tropicana 100% Real Orange Juice (1L)', 'Dairy & Beverages', 'Beverages', 'Tropicana', '/assets/milk bevrages.png', '₹140.00', '1 Litre Tetrapack', '100% JUICE', '#EA580C'],
            ['Brooke Bond Red Label Strong Tea (1kg)', 'Dairy & Beverages', 'Beverages', 'Red Label', '/assets/milk bevrages.png', '₹510.00', '1 Kg Carton', 'RICH AROMA', '#991B1B'],

            // Home & Kitchen
            ['Surf Excel Easy Wash Detergent Powder (5kg)', 'Home & Kitchen', 'Detergent & Fabric Care', 'Surf Excel', '/assets/kitcvhen.png', '₹650.00', '5 Kg Polybag', 'CLEANING MASTER', '#0891B2'],
            ['Prestige Aluminium Pressure Cooker (5L)', 'Home & Kitchen', 'Kitchen Cookware & Serveware', 'Prestige', '/assets/kitcvhen.png', '₹1,450.00', '1 Unit', 'KITCHENWARE', '#061427'],
            ['Vim Dishwash Liquid Gel Lemon (750ml)', 'Home & Kitchen', 'Utensils Cleaner', 'Vim', '/assets/kitcvhen.png', '₹185.00', '750ml Bottle', 'LEMON POWER', '#CA8A04'],
            ['Milton Thermosteel Flip Lid Water Bottle (1L)', 'Home & Kitchen', 'Kitchen Storage', 'Milton', '/assets/kitcvhen.png', '₹890.00', '1 Litre Bottle', 'HOT & COLD', '#475569'],

            // Personal Care
            ['Dettol Original Germ Protection Soap (Pack of 5)', 'Personal Care', 'Personal Hygiene', 'Dettol', '/assets/promo-summer.png', '₹240.00', '125g x 5 Soap Bars', 'HYGIENE SHIELD', '#16A34A'],
            ['Colgate Total Whole Mouth Protection (200g)', 'Personal Care', 'Oral Care', 'Colgate', '/assets/promo-summer.png', '₹135.00', '200g Saver Tube', '12HR DENTAL CARE', '#2563EB'],
            ['Head & Shoulders Smooth & Silky Shampoo (650ml)', 'Personal Care', 'Hair Care', 'Head & Shoulders', '/assets/promo-summer.png', '₹575.00', '650ml Pump Bottle', 'ANTI DANDRUFF', '#0284C7'],
            ['Nivea Soft Light Moisturizing Cream (300ml)', 'Personal Care', 'Skin Care', 'Nivea', '/assets/promo-summer.png', '₹390.00', '300ml Tub', 'SOFT SKIN', '#1D4ED8'],

            // Baby Care & Kids
            ['Pampers All Round Protection Baby Diapers (L 64s)', 'Baby Care & Kids', 'Diaper & Wipes', 'Pampers', '/assets/baby care.png', '₹999.00', '64 Diaper Pants', 'BABY CARE', '#9333EA'],
            ["Johnson's Baby No More Tears Shampoo (500ml)", 'Baby Care & Kids', 'Baby Care', "Johnson's", '/assets/baby care.png', '₹430.00', '500ml Bottle', 'GENTLE CARE', '#DB2777'],

            // Toys & Stationery
            ['Classmate Spiral Bound Notebook (A4 300 Pages)', 'Toys & Stationery', 'Stationery', 'Classmate', '/assets/stationary.png', '₹165.00', 'Single Book', 'OFFICE & SCHOOL', '#D97706'],
            ['Camel Student Acrylic Paint Set (12 Tubes)', 'Toys & Stationery', 'Stationery', 'Camel', '/assets/stationary.png', '₹240.00', '12 Color Tubes', 'ART & CRAFT', '#059669'],

            // Luggage & Travel
            ['American Tourister 3-Piece Luggage Trolley Set', 'Luggage & Travel', 'Luggage', 'American Tourister', '/assets/luggage.png', '₹7,999.00', '3 Trolley Bags', 'TRAVEL SUITCASE', '#475569'],
            ['Wildcraft Waterproof Campus Backpack (30L)', 'Luggage & Travel', 'Bags', 'Wildcraft', '/assets/luggage.png', '₹1,299.00', '30 Litre Backpack', 'DURABLE BAG', '#E4652E'],

            // Electronics
            ['Philips Smart HD LED TV (43 Inch)', 'Electronics', 'Home Appliances', 'Philips', '/assets/electronics.png', '₹24,990.00', '1 Unit', 'SMART TECH', '#0284C7'],
            ['Bajaj Rex 500W Mixer Grinder with 3 Jars', 'Electronics', 'Kitchen Appliances', 'Bajaj', '/assets/electronics.png', '₹2,199.00', '1 Unit + 3 Jars', '500W POWER', '#061427'],
            ['Orient Electric Fast-Air Room Heater Fan (2000W)', 'Electronics', 'Home Appliances', 'Orient', '/assets/electronics.png', '₹1,490.00', '1 Unit', 'QUICK HEATING', '#DC2626'],
        ];

        foreach ($products as $i => [$name, $categoryName, $variety, $brand, $image, $price, $packSize, $badge, $badgeBg]) {
            $category = Category::where('slug', Str::slug($categoryName))->firstOrFail();

            // Varieties the Categories page never listed still need a home.
            $subcategory = Subcategory::firstOrCreate(
                ['category_id' => $category->id, 'slug' => Str::slug($variety)],
                ['name' => $variety, 'sort_order' => 99],
            );

            Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'subcategory_id' => $subcategory->id,
                    'name' => $name,
                    'brand' => $brand,
                    'image' => $this->seedMedia($image, 'products'),
                    'price' => $price,
                    'pack_size' => $packSize,
                    'badge' => $badge,
                    'badge_bg' => $badgeBg,
                    'sort_order' => $i,
                ],
            );
        }
    }

    private function storeCategories(): array
    {
        return [
            [
                'name' => 'Grocery',
                'icon' => 'fa-wheat-awn',
                'home_label' => 'Grocery &<br>Staples',
                'image' => '/assets/grocery.PNG',
                'badge_bg' => '#E4652E',
                'subcategories' => [
                    'Rice & Rice Products', 'Oil & Ghee', 'Flours', 'Masala & Spices',
                    'Salt / Sugar / Jaggery', 'Dal & Pulses', 'Dry Fruits', 'Organic Food',
                ],
            ],
            [
                'name' => 'Fruits & Vegetables',
                'icon' => 'fa-carrot',
                'home_label' => 'Fruits &<br>Vegetables',
                'image' => '/assets/fruits and veggies.png',
                'badge_bg' => '#16A34A',
                'subcategories' => ['Fresh Vegetables', 'Fresh Fruits'],
            ],
            [
                'name' => 'Packaged Food',
                'icon' => 'fa-cookie',
                'home_label' => 'Packaged<br>Food',
                'image' => '/assets/promo-weekly.png',
                'badge_bg' => '#EA580C',
                'subcategories' => [
                    'Biscuits & Cookies', 'Breakfast Cereals', 'Chocolates & Candies',
                    'Ketchup & Sauces', 'Pasta & Noodles', 'Jams & Spreads',
                    'Instant Food', 'Snacks & Farsans',
                ],
            ],
            [
                'name' => 'Dairy & Beverages',
                'icon' => 'fa-bottle-water',
                'home_label' => 'Dairy &<br>Beverages',
                'image' => '/assets/milk bevrages.png',
                'badge_bg' => '#0284C7',
                'subcategories' => ['Dairy', 'Beverages', 'Breads'],
            ],
            [
                'name' => 'Home & Kitchen',
                'icon' => 'fa-utensils',
                'home_label' => 'Home &<br>Kitchen',
                'image' => '/assets/kitcvhen.png',
                'badge_bg' => '#061427',
                'subcategories' => [
                    'Detergent & Fabric Care', 'Utensils Cleaner', 'Freshners & Repellents',
                    'Kitchen Storage', 'Home Improvement', 'Kitchen Cookware & Serveware',
                    'Kitchen & Dining', 'Kitchen Essentials', 'Tissue Paper & Napkins',
                    'Pooja Needs', 'Bed & Bath',
                ],
            ],
            [
                'name' => 'Personal Care',
                'icon' => 'fa-pump-soap',
                'home_label' => 'Personal<br>Care',
                'image' => '/assets/promo-summer.png',
                'badge_bg' => '#0891B2',
                'subcategories' => [
                    'Oral Care', 'Skin Care', 'Hair Care', 'Face Care', 'Sanitary Napkins',
                    'Personal Hygiene', 'Deos & Perfumes', 'Beauty Products',
                    'Shaving Needs', 'Health & Wellness', 'Nail Care',
                ],
            ],
            [
                'name' => 'Baby Care & Kids',
                'icon' => 'fa-baby',
                'home_label' => 'Baby Care<br>& Kids',
                'image' => '/assets/baby care.png',
                'badge_bg' => '#9333EA',
                'subcategories' => ['Baby Care', 'Diaper & Wipes', 'Baby Food'],
            ],
            [
                'name' => 'Toys & Stationery',
                'icon' => 'fa-pen-ruler',
                'home_label' => 'Toys &<br>Stationery',
                'image' => '/assets/stationary.png',
                'badge_bg' => '#D97706',
                'subcategories' => ['Stationery', 'Art & Craft', 'Board & Educational Games', 'Soft Toys'],
            ],
            [
                'name' => 'Luggage & Travel',
                'icon' => 'fa-suitcase-rolling',
                'home_label' => 'Luggage<br>& Travel',
                'image' => '/assets/luggage.png',
                'badge_bg' => '#475569',
                'subcategories' => ['Luggage', 'Bags'],
            ],
            [
                'name' => 'Electronics',
                'icon' => 'fa-plug',
                'home_label' => 'Electronics',
                'image' => '/assets/electronics.png',
                'badge_bg' => '#0284C7',
                'subcategories' => ['Mobiles', 'Watches', 'Home Appliances', 'Computer Peripherals'],
            ],
        ];
    }
}
