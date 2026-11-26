<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\PromotionsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\StoresController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Jay Tulsi Agency & Vinayak Stores
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/stores', [StoresController::class, 'index'])->name('stores');
Route::get('/categories', [CategoriesController::class, 'index'])->name('categories');
// Both category pages deep-link their subcategory chips here as
// /products?category=…&variety=…, so this must stay registered.
Route::get('/products', [ProductsController::class, 'index'])->name('products');
// One product on its own page. Registered after /products so the grid keeps
// that exact path, and the slug never swallows it.
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('product');
Route::get('/b2b-categories', [CategoriesController::class, 'b2bIndex'])->name('b2b-categories');
Route::get('/promotions', [PromotionsController::class, 'index'])->name('promotions');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/cart', fn () => \Inertia\Inertia::render('Cart'))->name('cart');
Route::get('/checkout', fn () => \Inertia\Inertia::render('Checkout'))->name('checkout');
// Master catalogue PDFs (retail | b2b), uploaded from the admin panel.
Route::get('/catalogue/{type}/download', [CatalogueController::class, 'download'])->name('catalogue.download');
Route::post('/inquiry', [InquiryController::class, 'store'])->name('inquiry.store');
// Type-ahead for the header search box.
Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');

// Served by the app, not from public/, so both carry the live domain and the
// sitemap follows the catalogue.
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
