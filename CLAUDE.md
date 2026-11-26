# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Marketing/catalogue site for **Jay Tulsi Agency & Vinayak Stores** (Surat, India) — retail supermarket chain + B2B commercial supply.

Two halves in one Laravel app:
- **Storefront** — public, no auth: Laravel + Inertia 2 + Vue 3 SPA.
- **Admin** — Filament 4 panel at `/admin`, auth required. Every piece of storefront content (hero slides, products, promotions, stores, logos, certificates, phone numbers) is edited here and stored in MySQL.

Stack: PHP 8.2+, Laravel 11/12 (slim skeleton), Filament 4, Inertia 2, Vue 3, Intervention Image. Database is **MySQL** (`jaytulsi_db` by default) — the `database/database.sqlite` file is a leftover and unused.

## Commands

```bash
php artisan serve      # Laravel on :8000 (storefront + /admin)
npm run dev            # Vite dev server on :5173 (required — the Blade view uses @vite)
npm run build          # production build into public/build/
./vendor/bin/pint      # PHP formatter (the only linter configured)

php artisan migrate --seed   # full DB rebuild; DatabaseSeeder seeds admin user, settings, catalogue, home, marketing, stores
php artisan storage:link     # uploaded images are served from storage/app/public
```

Both servers are usually already running during a session — check `public/hot` and the ports before starting your own.

**There is no test suite.** PHPUnit is a dev dependency but there is no `tests/` directory and no `phpunit.xml`. Verify changes by loading the real pages in a browser, and admin changes at `/admin`.

## Storefront architecture

**Request flow:** `routes/web.php` → thin controller → `Inertia::render('PageName', [...props])` → `resources/js/Pages/PageName.vue`, wrapped by `resources/views/app.blade.php` (the only storefront Blade file). `resources/js/app.js` resolves pages via `import.meta.glob('./Pages/**/*.vue')`.

**Content comes from Eloquent models, not arrays.** Controllers query models and map them to camelCase prop arrays for Vue — see [HomeController.php](app/Http/Controllers/HomeController.php), which assembles hero slides, stats, both category panels, home promotions, client/brand logos and certificates in one `index()`. The mapping layer is deliberate: DB columns stay snake_case, Vue props stay camelCase. Adding a field means touching the migration, the model's `$fillable`, the Filament form, the controller's map, and the Vue component.

**Shared model conventions** (`app/Support/`, `app/Traits/`):
- `Publishable` — `scopeActive()` (`is_active`) + `scopeOrdered()` (`sort_order`, then `id`). Nearly every content query is `Model::active()->ordered()->get()`.
- `HasMediaUrl` — `mediaUrl()` passes through absolute URLs and `/assets/...` paths untouched (that's how seeded content works) but resolves anything else through the `public` disk (that's how admin uploads work). Models expose it as `image_url` / `logo_image_url` accessors.
- `CompressesUploadedImages` — a model with `protected array $compressibleImages = ['image']` gets its uploads re-encoded by [ImageCompressor](app/Services/ImageCompressor.php) on save, but only for columns that actually changed.

**Never name a local scope after a query-builder method.** `Builder::__call()` checks named scopes *before* forwarding to the underlying query builder, so `scopeForPage()` on a model silently hijacks `forPage($page, $perPage)` — which `paginate()` calls internally. `UspCard` had exactly that, and every Filament list of it showed a handful of rows above a footer counting all of them. The scope is now `scopeShownOn()`. The same trap applies to `take`, `limit`, `find`, `where*`, `latest` and friends; see also the `Setting::get()` note below.

**Site-wide props are shared, not passed.** [HandleInertiaRequests](app/Http/Middleware/HandleInertiaRequests.php) injects `site` (phones, emails, addresses, hours, socials) and `whatsapp` (enabled flag, number, prebuilt `wa.me` URL) into every response, read from the `settings` table. Chrome components (`TopBar`, `Footer`, `QuickCalloutBanner`, `WhatsAppFloat`) consume these directly — do **not** add these to individual controllers.

**Settings are a key/value table** with a forever-cached map. Read with `Setting::val('key', $default)` or `Setting::bool('key')`; the cache self-invalidates on save/delete. Never call `Setting::get()`/`Setting::value()` — those are Eloquent builder methods and the model deliberately avoids shadowing them.

**Some DB content contains HTML** and is rendered with `v-html`. Hero titles run through `nl2br()` plus a `*highlighted*` → `<span class="hero-title-gradient shiny-text">` transform in the controller; category tile `home_label` may carry a `<br>` to keep tiles two lines tall. Plain-text substitution on these values will drop styling.

**No shared layout component.** Each page in `resources/js/Pages/` composes its own chrome: `<TopBar />`, `<Navbar :activePage="..." />`, sections, `<QuickCalloutBanner />`, `<Footer />`, `<ContactModal :isOpen="isModalOpen" />`, `<WhatsAppFloat />`. Every page owns an `isModalOpen` ref. A change to page chrome must be repeated across all **seven** pages (Home, About, Stores, Categories, B2bCategories, Products, Promotions).

**Scroll-reveal animation** is an `IntersectionObserver` in each page's `onMounted` that adds `.active` to `.reveal`, `.reveal-left`, `.reveal-right`, `.reveal-scale`, then unobserves. This block is duplicated verbatim in all seven pages — new animated markup just needs one of those classes on it, no JS change.

**Forms submit for real.** `ContactModal.vue` and `BulkInquiryForm.vue` validate locally, then `axios.post('/inquiry', …)` → [InquiryController](app/Http/Controllers/InquiryController.php) validates via `InquiryRequest`, persists an `Inquiry`, and returns a prefilled `api.whatsapp.com` URL that the client redirects to. Submissions show up in the admin under Inquiries. Phone validation convention on both sides: 10 digits starting 6–9.

**The `meta` prop** (title/description) is still passed by every controller but no page consumes it — the `<title>` is hardcoded in [app.blade.php](resources/views/app.blade.php) and Inertia's `<Head>` is not used anywhere.

## Admin panel (Filament 4)

Configured in [AdminPanelProvider.php](app/Providers/Filament/AdminPanelProvider.php): SPA mode, custom login page, brand orange `#FA5400`, dark mode **off** (`admin.css` only styles the light shell), and five navigation groups — Home Page, Catalogue, Marketing, Business, System. Resources, pages and widgets are all auto-discovered, so a new resource appears simply by existing in the right namespace.

**Every resource follows the same four-part split** (see `app/Filament/Resources/HeroSlides/` as the reference): `XResource.php` (model, icon, `$navigationGroup`, `$navigationSort`, labels, page routes) + `Schemas/XForm.php` + `Tables/XTable.php` + `Pages/{List,Create,Edit}X.php`. Copy that shape rather than generating a single-file resource.

- Panel styling lives in `public/css/admin.css`, injected via a `HEAD_END` render hook and cache-busted on the file's own mtime — no rebuild needed after editing it. The storefront never loads it.
- Extra admin surfaces: an [ImageCompressorPage](app/Filament/Pages/ImageCompressorPage.php) for bulk-compressing existing uploads, and dashboard widgets (`StatsOverview`, `ContentHealth`, `CatalogueChart`, `RecentInquiries`).
- Uploads land in `storage/app/public/<hero|products|categories|logos|panels|promotions|stores|certificates>/`. Adding a new upload directory means adding it to `ImageCompressor::UPLOAD_DIRS` too, or bulk compression will skip it.

## Styling

One global stylesheet does nearly all the work: **`style.css` at the repo root** (~4300 lines). `resources/css/style.css` is a two-line file that `@import`s it, and that import is the Vite CSS entry — so edit the root `style.css`, never the resources one. No Tailwind, no preprocessor.

- Design tokens (brand orange `#FA5400`, navy `#061427`, shadows, radii, easing curves, hover-lift distances) are CSS custom properties in `:root` at the top of `style.css`. Use them rather than new literals.
- Component-specific CSS lives in scoped `<style>` blocks inside the `.vue` files; global/shared classes live in `style.css`. Both are in play for most components.
- Reusable animation utilities are defined once around [style.css:339-423](style.css#L339-L423): the four `.reveal*` entrances, plus `.stagger` / `.stagger-fast` containers that make `.reveal` children rise one after another. The stagger runs as an *animation* with `backwards` fill rather than a `transition-delay`, so hover stays instant — keep that pattern if you extend it.
- Responsive rules are **not** centralized: `style.css` has per-section media queries, then large `TABLET (max 1024px)` / `max 768px` / `max 480px` blocks, then a separate `GLOBAL MOBILE RESPONSIVENESS` section near the end. A mobile fix often needs checking in more than one of these, and later blocks may already override you. Note some decorative elements (`.watermark-backdrop`, `.b2b-backdrop-glow`) are `display:none` below 768px.
- Fonts (Outfit, Plus Jakarta Sans, Playfair Display) and Font Awesome 6 load from CDN in `app.blade.php`. Icons are inline `<i class="fa-solid fa-...">` — icon names are also stored as DB strings on categories, stats and stores.

**Layout must not shift.** The standing rule on this project is that visual polish may not move geometry — see the project memory on verifying layout is unchanged for the `getBoundingClientRect()` diff method and the motion traps that produce false mismatches.

Do not create standalone `.html` preview files to demo UI work; change the real source and check the running app.

## Assets and build output

- Static images are duplicated in `assets/` (repo root) and `public/assets/`. Templates and seeded DB rows reference `/assets/...`, i.e. the **`public/` copy** — put new static images there. Admin-uploaded images go through the `public` disk instead and are served from `/storage/...`.
- Some existing filenames have spaces or uppercase extensions (`fruits and veggies.png`, `grocery.PNG`). These work locally on Windows but are case-sensitive on a Linux host; prefer lowercase-hyphenated names for anything new.
- `public/build/` **is committed to git**, so `npm run build` produces tracked file churn (new content-hashed filenames plus `manifest.json`). Expect the old hashed files to show as deletions.
- `bootstrap/cache/packages.php` and `services.php` are also tracked, and **must never be committed again**. Laravel and composer rewrite both on every environment, so the deployed copies are permanently modified against the repo; a commit that touches either path makes the server's `git pull` abort with *"your local changes would be overwritten"*, and the deploy panel there has no way to delete a file or discard a change. Leave them untouched — `composer install` is what keeps the deployed copies right. If a `git status` here shows them modified (running `package:discover`, adding a package), `git checkout --` them before committing.

## Legacy

`script.js` at the repo root is dead code from the pre-Inertia static site (nothing references it); the old `index.html` / `about.html` have been deleted. The root `style.css`, by contrast, is very much live.
