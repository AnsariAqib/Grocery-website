<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Widgets\CatalogueChart;
use App\Filament\Widgets\ContentHealth;
use App\Filament\Widgets\RecentInquiries;
use App\Filament\Widgets\StatsOverview;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->spa()
            ->login(Login::class)
            // "My Account" in the user menu: name, email and a password change
            // that asks for the current password first. Rendered inside the
            // panel shell (isSimple: false) so the sidebar stays put.
            ->profile(isSimple: false)
            ->brandName('Jay Tulsi Agency')
            ->brandLogo(asset('assets/logo.png'))
            ->brandLogoHeight('2.25rem')
            ->favicon(asset('assets/logo.png'))
            ->colors([
                // The site's brand orange (--primary-orange in style.css).
                'primary' => Color::hex('#E4652E'),
            ])
            // Matches the storefront body font; headings switch to Outfit in admin.css.
            ->font('Plus Jakarta Sans')
            // admin.css styles the light shell only — leaving the toggle on would
            // hand users a half-styled dark theme.
            ->darkMode(false)
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::SevenExtraLarge)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->navigationGroups([
                NavigationGroup::make('Home Page')->icon('heroicon-o-home'),
                NavigationGroup::make('Catalogue')->icon('heroicon-o-squares-2x2'),
                NavigationGroup::make('Marketing')->icon('heroicon-o-megaphone'),
                NavigationGroup::make('Business')->icon('heroicon-o-building-storefront'),
                NavigationGroup::make('System')->icon('heroicon-o-cog-6-tooth'),
            ])
            ->userMenuItems([
                Action::make('viewSite')
                    ->label('View website')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url('/', shouldOpenInNewTab: true),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                StatsOverview::class,
                ContentHealth::class,
                CatalogueChart::class,
                RecentInquiries::class,
            ])
            // Loaded last in <head> so these overrides win over Filament's own
            // stylesheet without needing !important. Panel pages only — the
            // storefront never sees this file.
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => $this->brandStyles(),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => view('filament.custom-loader')->render(),
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * Outfit for headings (Filament's ->font() covers the body face), Font
     * Awesome for the icon pickers, plus the panel theme — cache-busted on the
     * file's own mtime so edits show up without asking anyone to hard-refresh.
     *
     * Font Awesome is the same 6.4.0 build the storefront loads. Without it the
     * icon <select> options (categories, USP cards, order steps, stats) render
     * their label with an empty box where the glyph should be, which makes
     * picking an icon guesswork.
     */
    private function brandStyles(): string
    {
        $path = public_path('css/admin.css');
        $version = is_file($path) ? filemtime($path) : null;

        return <<<HTML
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link rel="stylesheet" href="{$this->assetUrl('css/admin.css', $version)}">
            HTML;
    }

    private function assetUrl(string $path, ?int $version): string
    {
        return asset($path).($version ? '?v='.$version : '');
    }
}
