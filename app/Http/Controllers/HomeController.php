<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryPanel;
use App\Models\Certificate;
use App\Models\GalleryImage;
use App\Models\HeroSlide;
use App\Models\Logo;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Stat;
use App\Models\Testimonial;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * The home page shows a fixed 10 tiles per category panel regardless of how
     * many categories exist, so the section keeps its shape as the catalogue grows.
     */
    private const TILES_PER_PANEL = 10;

    public function index(): Response
    {
        return Inertia::render('Home', [
            'meta' => [
                'title' => 'VIZORA FOODS | Fresh • Local • Organic Groceries',
                'description' => 'Surat\'s supermarket chain and authorised B2B supply partner since 1984 — groceries, dairy, FMCG, housekeeping and industrial supplies for homes, retailers, offices and institutions across Hazira, Icchapore and Kavas.',
            ],
            'heroSlides' => HeroSlide::active()->ordered()->get()->map(fn (HeroSlide $slide) => [
                'image' => $slide->image_url,
                'title' => nl2br(preg_replace('/\*(.*?)\*/', '<span class="hero-title-gradient shiny-text">$1</span>', $slide->title_html)),
                'subtext' => $slide->subtext,
                'btnPrimaryText' => $slide->btn_primary_text,
                'btnPrimaryLink' => $slide->btn_primary_link,
                'btnPrimaryHasArrow' => $slide->btn_primary_has_arrow,
                'btnNavyText' => $slide->btn_navy_text,
                'btnNavyLink' => $slide->btn_navy_link,
                'btnNavyHasArrow' => $slide->btn_navy_has_arrow,
            ])->values(),
            'stats' => Stat::active()->ordered()->get()->map(fn (Stat $stat) => [
                'label' => $stat->label,
                'target' => $stat->target,
                'suffix' => $stat->suffix,
                'icon' => $stat->icon,
            ])->values(),
            'panels' => [
                'store' => $this->panel(Category::TYPE_STORE),
                'b2b' => $this->panel(Category::TYPE_B2B),
            ],
            'homePromotions' => Promotion::active()->onHome()->ordered()->get()
                ->map(fn (Promotion $promotion) => PromotionsController::schedule($promotion) + [
                    'id' => $promotion->id,
                    'badge' => $promotion->badge,
                    'badgeColor' => $promotion->badge_bg,
                    'title' => $promotion->title,
                    'description' => $promotion->description,
                    'ctaText' => $promotion->cta_text,
                    'ctaUrl' => $promotion->cta_url,
                    'image' => $promotion->image_url,
                    'validity' => $promotion->validity,
                ])->values(),
            // Showcases below the promotions strip. All three are admin-driven:
            // the two "Featured" toggles and the Gallery resource.
            'featuredCategories' => Category::active()->featured()->ordered()->get()
                ->map(fn (Category $category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'type' => $category->type,
                    'icon' => $category->icon,
                    'iconColor' => $category->icon_color,
                    'image' => $category->image_url,
                    'href' => '/products?category='.rawurlencode($category->name),
                ])->values(),
            'featuredProducts' => Product::active()->featured()
                ->with('subcategory.category')
                ->ordered()
                ->limit(8)
                ->get()
                ->filter(fn (Product $product) => $product->subcategory?->is_active
                    && $product->subcategory->category?->is_active)
                ->map(fn (Product $product) => $product->toCardArray())
                ->values(),
            // Photos and videos are handed over separately: the rail beside the
            // Featured sections is a photo rail, and the gallery below prints
            // them under their own headings.
            'galleryImages' => GalleryImage::active()->photos()->ordered()->get()
                ->map(fn (GalleryImage $image) => $image->toCardArray())->values(),
            'galleryVideos' => GalleryImage::active()->videos()->ordered()->get()
                ->map(fn (GalleryImage $video) => $video->toCardArray())
                // A link that YouTube cannot resolve would render a blank frame.
                ->filter(fn (array $video) => filled($video['embedUrl']))
                ->values(),
            'testimonials' => Testimonial::active()->ordered()->get()
                ->map(fn (Testimonial $testimonial) => $testimonial->toCardArray())->values(),
            'clientLogos' => $this->logos(Logo::TYPE_CLIENT),
            'brandLogos' => $this->logos(Logo::TYPE_BRAND),
            'certificates' => Certificate::active()->ordered()->get()
                ->map(fn (Certificate $certificate) => [
                    'id' => $certificate->id,
                    'type' => $certificate->type,
                    'brand' => $certificate->brand_name,
                    'title' => $certificate->title,
                    'description' => $certificate->description,
                    'signatory' => $certificate->signatory,
                    'logo' => $certificate->logo_image_url,
                    'fullImage' => $certificate->certificate_image_url,
                    'pdf' => $certificate->pdf_url,
                ])->values(),
        ]);
    }

    private function logos(string $type): Collection
    {
        return Logo::active()->ofType($type)->ordered()->get()
            ->map(fn (Logo $logo) => [
                'id' => $logo->id,
                'name' => $logo->name,
                'image' => $logo->image_url,
            ])->values();
    }

    private function panel(string $type): ?array
    {
        $panel = CategoryPanel::where('type', $type)->where('is_active', true)->first();

        if (! $panel) {
            return null;
        }

        return [
            'badgeText' => $panel->badge_text,
            'heading' => $panel->heading,
            'subheading' => $panel->subheading,
            'artImage' => $panel->art_image_url,
            'ctaText' => $panel->cta_text,
            // Blank means the button opens the enquiry form, which is what it
            // did before there was anywhere to put a link.
            'ctaUrl' => $panel->cta_url,
            'tiles' => Category::active()->ofType($type)->ordered()
                ->limit(self::TILES_PER_PANEL)
                ->get()
                ->map(fn (Category $category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    // May contain a <br> so the tile keeps its two-line height.
                    'label' => $category->home_label ?: $category->name,
                    'icon' => $category->icon,
                    'iconColor' => $category->icon_color,
                ])->values(),
        ];
    }
}
