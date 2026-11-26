<?php

namespace App\Support;

/**
 * The typography choices offered in the admin, in one place.
 *
 * Each entry feeds three things at once: the Filament select options, the
 * Google Fonts URL built in app.blade.php, and the CSS stack injected into
 * :root. Adding a family means adding one entry here and nothing else.
 *
 * Requests stop at 800: that is the heaviest weight any themed rule asks for.
 * The two 900 rules in style.css sit on `.ap-mark` / `.ap-text`, which use
 * generic serif/sans-serif and never touch these families.
 *
 * Within that range each entry lists only the weights the family actually
 * ships. Google silently drops the rest — asking Lato for 500/600/800 still
 * returns 200, just without those faces — so the lists were checked against
 * the API rather than copy-pasted.
 *
 * Where a family stops short of 800 the browser substitutes the nearest face
 * it has, which is why the labels say so: Lato jumps 400 -> 700, Cormorant
 * Garamond tops out at 700, and DM Serif Display ships a single 400 that gets
 * synthesised into the bold the headings ask for. Those are display choices,
 * not defects, but the admin should not have to discover them by saving.
 */
class FontOptions
{
    public const DEFAULT_HEADING = 'outfit';

    public const DEFAULT_BODY = 'plus-jakarta-sans';

    /**
     * Loaded on every page regardless of what the admin picked: the certificate
     * script and signature are deliberately Playfair italic and are part of the
     * artwork, not the theme. Kept to the two faces those rules use — picking
     * Playfair as a site font swaps in the fuller weight range below.
     */
    private const ALWAYS_LOADED = [
        'playfair-display' => 'Playfair+Display:ital,wght@0,700;1,700',
    ];

    /**
     * key => [label, CSS stack, Google Fonts family query]
     */
    private const FONTS = [
        // ---- Body / UI workhorses ------------------------------------
        'inter' => [
            'Inter — neutral, very readable',
            "'Inter', sans-serif",
            'Inter:wght@400;500;600;700;800',
        ],
        'plus-jakarta-sans' => [
            'Plus Jakarta Sans (current body)',
            "'Plus Jakarta Sans', sans-serif",
            'Plus+Jakarta+Sans:wght@400;500;600;700;800',
        ],
        'dm-sans' => [
            'DM Sans — clean, geometric',
            "'DM Sans', sans-serif",
            'DM+Sans:wght@400;500;600;700;800',
        ],
        'manrope' => [
            'Manrope — soft, modern',
            "'Manrope', sans-serif",
            'Manrope:wght@400;500;600;700;800',
        ],
        'work-sans' => [
            'Work Sans — sturdy, practical',
            "'Work Sans', sans-serif",
            'Work+Sans:wght@400;500;600;700;800',
        ],
        'nunito-sans' => [
            'Nunito Sans — friendly, rounded',
            "'Nunito Sans', sans-serif",
            'Nunito+Sans:wght@400;500;600;700;800',
        ],
        'lato' => [
            'Lato — classic, warm (no 500/600/800)',
            "'Lato', sans-serif",
            'Lato:wght@400;700',
        ],
        'open-sans' => [
            'Open Sans — safe, familiar',
            "'Open Sans', sans-serif",
            'Open+Sans:wght@400;500;600;700;800',
        ],
        'roboto' => [
            'Roboto — standard, compact',
            "'Roboto', sans-serif",
            'Roboto:wght@400;500;600;700;800',
        ],
        'mulish' => [
            'Mulish — light, airy',
            "'Mulish', sans-serif",
            'Mulish:wght@400;500;600;700;800',
        ],
        'figtree' => [
            'Figtree — friendly, current',
            "'Figtree', sans-serif",
            'Figtree:wght@400;500;600;700;800',
        ],
        'public-sans' => [
            'Public Sans — plain, official',
            "'Public Sans', sans-serif",
            'Public+Sans:wght@400;500;600;700;800',
        ],

        // ---- Heading / display ---------------------------------------
        'outfit' => [
            'Outfit (current heading)',
            "'Outfit', sans-serif",
            'Outfit:wght@400;500;600;700;800',
        ],
        'poppins' => [
            'Poppins — round, bold headings',
            "'Poppins', sans-serif",
            'Poppins:wght@400;500;600;700;800',
        ],
        'montserrat' => [
            'Montserrat — wide, confident',
            "'Montserrat', sans-serif",
            'Montserrat:wght@400;500;600;700;800',
        ],
        'urbanist' => [
            'Urbanist — sleek, narrow',
            "'Urbanist', sans-serif",
            'Urbanist:wght@400;500;600;700;800',
        ],
        'sora' => [
            'Sora — technical, distinctive',
            "'Sora', sans-serif",
            'Sora:wght@400;500;600;700;800',
        ],
        'space-grotesk' => [
            'Space Grotesk — quirky, modern (no 800)',
            "'Space Grotesk', sans-serif",
            'Space+Grotesk:wght@400;500;600;700',
        ],
        'bricolage-grotesque' => [
            'Bricolage Grotesque — characterful',
            "'Bricolage Grotesque', sans-serif",
            'Bricolage+Grotesque:wght@400;500;600;700;800',
        ],
        'playfair-display' => [
            'Playfair Display — elegant serif',
            "'Playfair Display', Georgia, serif",
            'Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;1,700',
        ],
        'cormorant-garamond' => [
            'Cormorant Garamond — classic serif (no 800)',
            "'Cormorant Garamond', Georgia, serif",
            'Cormorant+Garamond:wght@400;500;600;700',
        ],
        'dm-serif-display' => [
            'DM Serif Display — headlines only, one weight',
            "'DM Serif Display', Georgia, serif",
            'DM+Serif+Display:wght@400',
        ],
    ];

    /**
     * key => label, for the Filament select.
     */
    public static function options(): array
    {
        return array_map(fn (array $font) => $font[0], self::FONTS);
    }

    /**
     * CSS font stack for a stored setting value.
     *
     * Falls back to $fallbackKey for anything unrecognised, so a deleted or
     * hand-edited settings row degrades to the shipped default instead of
     * emitting a broken declaration.
     */
    public static function stack(?string $key, string $fallbackKey = self::DEFAULT_BODY): string
    {
        return (self::FONTS[$key] ?? self::FONTS[$fallbackKey])[1];
    }

    /**
     * The full Google Fonts stylesheet URL for the chosen families.
     *
     * Unrecognised keys are dropped rather than defaulted: the caller resolves
     * the stack separately, and a missing family is better than a 404 request.
     */
    public static function googleUrl(string ...$keys): string
    {
        $families = self::ALWAYS_LOADED;

        foreach ($keys as $key) {
            if (isset(self::FONTS[$key])) {
                // A selected family overrides its always-loaded stub, so
                // picking Playfair widens it instead of requesting it twice.
                $families[$key] = self::FONTS[$key][2];
            }
        }

        // css2 documents families as alphabetical; cheap to honour.
        sort($families);

        return 'https://fonts.googleapis.com/css2?family='
            .implode('&family=', $families)
            .'&display=swap';
    }
}
