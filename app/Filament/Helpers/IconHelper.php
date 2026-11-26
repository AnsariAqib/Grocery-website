<?php

namespace App\Filament\Helpers;

class IconHelper
{
    /**
     * Returns a curated list of FontAwesome solid icons for the Supermarket / Agency app.
     * The key is the icon class, and the value is the HTML representation with the icon name.
     */
    public static function getFontAwesomeSolidIcons(): array
    {
        $icons = [
            'fa-store' => 'Store',
            'fa-shop' => 'Shop',
            'fa-cart-shopping' => 'Shopping Cart',
            'fa-basket-shopping' => 'Shopping Basket',
            'fa-bag-shopping' => 'Shopping Bag',
            'fa-truck-fast' => 'Fast Delivery (Truck)',
            'fa-box' => 'Box',
            'fa-boxes-stacked' => 'Boxes Stacked',
            'fa-tag' => 'Tag',
            'fa-tags' => 'Tags',
            'fa-barcode' => 'Barcode',
            'fa-qrcode' => 'QR Code',
            'fa-receipt' => 'Receipt',
            'fa-credit-card' => 'Credit Card',
            'fa-wallet' => 'Wallet',
            'fa-money-bill' => 'Money Bill',
            'fa-money-bills' => 'Money Bills',
            'fa-coins' => 'Coins',
            'fa-percent' => 'Percent (Discount)',
            'fa-gift' => 'Gift',
            'fa-apple-whole' => 'Apple',
            'fa-carrot' => 'Carrot',
            'fa-lemon' => 'Lemon',
            'fa-pepper-hot' => 'Hot Pepper',
            'fa-bread-slice' => 'Bread Slice',
            'fa-cheese' => 'Cheese',
            'fa-egg' => 'Egg',
            'fa-bacon' => 'Bacon',
            'fa-drumstick-bite' => 'Meat',
            'fa-fish' => 'Fish',
            'fa-ice-cream' => 'Ice Cream',
            'fa-mug-hot' => 'Hot Drink / Coffee',
            'fa-wine-bottle' => 'Wine Bottle',
            'fa-bottle-water' => 'Water Bottle',
            'fa-cookie' => 'Cookie',
            'fa-bowl-food' => 'Bowl of Food',
            'fa-bowl-rice' => 'Bowl of Rice',
            'fa-wheat-awn' => 'Wheat (Grain)',
            'fa-leaf' => 'Leaf (Organic)',
            'fa-seedling' => 'Seedling (Plant)',
            'fa-snowflake' => 'Snowflake (Frozen)',
            'fa-fire' => 'Fire (Hot)',
            'fa-droplet' => 'Droplet (Liquid)',
            'fa-pump-soap' => 'Soap / Cleaning',
            'fa-spray-can' => 'Spray Can',
            'fa-jug-detergent' => 'Detergent',
            'fa-broom' => 'Broom (Cleaning)',
            'fa-toilet-paper' => 'Toilet Paper',
            'fa-tooth' => 'Tooth (Dental Care)',
            'fa-pills' => 'Pills (Pharmacy)',
            'fa-star' => 'Star',
            'fa-heart' => 'Heart',
            'fa-thumbs-up' => 'Thumbs Up',
            'fa-award' => 'Award',
            'fa-certificate' => 'Certificate',
            'fa-medal' => 'Medal',
            'fa-crown' => 'Crown',
            'fa-bolt' => 'Lightning Bolt',
            'fa-bullhorn' => 'Megaphone (Promo)',
            'fa-truck' => 'Truck',
            'fa-building' => 'Building (B2B)',
            'fa-city' => 'City',
            'fa-industry' => 'Industry / Factory',
            'fa-handshake' => 'Handshake (Deal)',
            'fa-briefcase' => 'Briefcase (Business)',
            'fa-users' => 'Users / Group',
            'fa-user-tie' => 'Professional / Admin',
        ];

        $options = [];
        foreach ($icons as $class => $name) {
            // Provide a rich HTML preview for Filament Select options
            $options[$class] = "<div class='flex items-center gap-2'>
                <div class='flex items-center justify-center w-6 h-6 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300'>
                    <i class='fa-solid {$class}'></i>
                </div>
                <span>{$name}</span>
            </div>";
        }

        return $options;
    }
}
