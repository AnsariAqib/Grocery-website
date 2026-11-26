<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Support\FontOptions;
use App\Support\Seo;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ---- Contact -------------------------------------------------
            ['contact', 'phone_primary', '9879197739', 'phone', 'Primary phone', 'Shown in the top bar, footer and helpline.'],
            ['contact', 'phone_secondary', '9090081417', 'phone', 'Secondary phone', 'Leave blank to show only the primary number.'],
            ['contact', 'email_primary', 'jaytulsi.info@gmail.com', 'email', 'Primary email', null],
            ['contact', 'email_secondary', 'navdeepk97@gmail.com', 'email', 'Secondary email', 'Footer only. Leave blank to hide.'],
            ['contact', 'address_short', 'Surat, Gujarat, India', 'text', 'Short address', 'Top bar and the contact callout band.'],
            ['contact', 'address_footer', 'Surat, India', 'text', 'Footer address', 'Kept short so the footer contact card stays on one line.'],
            ['contact', 'address_full', 'GF01, AMNS Shopping Complex, AMNS Township, Hazira, Surat - 394270', 'textarea', 'Full address', null],
            ['contact', 'store_hours', 'Mon - Sun: 8:00 AM - 10:00 PM', 'text', 'Store & dispatch hours', null],

            // ---- Business (Contact Us page) ------------------------------
            ['business', 'business_legal_name', 'Jay Tulsi Agency', 'text', 'Registered business name', 'Shown in the Business Information block on the Contact page.'],
            ['business', 'business_gst', '', 'text', 'Company GST number', 'Leave blank to hide the row.'],
            ['business', 'business_established', '1984', 'text', 'In business since', null],
            ['business', 'business_service_area', 'Surat, Hazira industrial belt & PAN India B2B supply', 'text', 'Service area', null],
            ['business', 'business_working_days', 'Monday to Sunday', 'text', 'Working days', null],

            // ---- WhatsApp ------------------------------------------------
            ['whatsapp', 'whatsapp_enabled', '1', 'boolean', 'Show WhatsApp button', 'Turns the floating chat button on the whole site on or off.'],
            ['whatsapp', 'whatsapp_number', '919879197739', 'phone', 'WhatsApp number', 'With country code and no spaces, e.g. 919879197739.'],
            ['whatsapp', 'whatsapp_message', 'Hello Jay Tulsi Agency, I would like to enquire about your products.', 'textarea', 'Pre-filled message', 'Text the customer sees already typed in WhatsApp.'],

            // ---- Social --------------------------------------------------
            ['social', 'social_facebook', '', 'url', 'Facebook URL', 'Full link, or just the page name. Leave blank and the icon is hidden.'],
            ['social', 'social_instagram', '', 'url', 'Instagram URL', 'Full link, or just the handle. Leave blank and the icon is hidden.'],
            ['social', 'social_linkedin', '', 'url', 'LinkedIn URL', 'Full link, or just the company name. Leave blank and the icon is hidden.'],

            // ---- Images --------------------------------------------------
            ['images', 'image_compression_enabled', '1', 'boolean', 'Enable image compression', 'When enabled, uploaded images are automatically compressed to save space and improve page speed.'],
            ['images', 'image_compression_quality', '82', 'text', 'Compression quality (1-100)', 'Higher = better quality but larger files. Recommended: 75–85. Default: 82 (Google\'s sweet spot).'],

            // ---- Footer --------------------------------------------------
            ['footer', 'footer_tagline', 'Retail Supermarket & B2B Supply Company delivering quality products and reliable solutions across our stores and business network.', 'textarea', 'Footer tagline', null],

            // ---- SEO -----------------------------------------------------
            ['seo', 'seo_default_title', Seo::DEFAULT_TITLE, 'text', 'Default page title', 'Used on any page that has no title of its own. Keep it under about 60 characters — Google cuts the rest off.'],
            ['seo', 'seo_default_description', Seo::DEFAULT_DESCRIPTION, 'textarea', 'Default meta description', 'The grey line under the title in Google results, and the caption on a shared link. Aim for 150–160 characters.'],
            ['seo', 'seo_og_image', '/assets/logo.png', 'text', 'Link preview image', 'The picture WhatsApp and Facebook show when someone shares the site. 1200x630 works best. A path like /assets/banner.jpg or a full URL.'],
            ['seo', 'seo_google_verification', '', 'text', 'Google verification code', 'Only the code from Google Search Console\'s HTML tag method, not the whole tag. Leave blank until you have one.'],

            // ---- Typography ----------------------------------------------
            ['typography', 'font_heading', FontOptions::DEFAULT_HEADING, 'select', 'Heading font', 'Used for section titles, page headings, navbar, card names and buttons across the whole site.'],
            ['typography', 'font_body', FontOptions::DEFAULT_BODY, 'select', 'Body font', 'Used for paragraphs, descriptions, form fields and footer text. Pick something plain here — it carries the long text.'],
        ];

        foreach ($settings as $i => [$group, $key, $value, $type, $label, $help]) {
            Setting::updateOrCreate(
                ['key' => $key],
                compact('group', 'value', 'type', 'label', 'help') + ['sort_order' => $i],
            );
        }

        Setting::forgetCache();
    }
}
