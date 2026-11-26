<template id="filament-spa-skeleton-template">
    <div class="fi-spa-skeleton-instance p-6 space-y-6 md:p-8 animate-fade-in">
        <!-- Breadcrumbs & Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4">
            <div class="space-y-3">
                <!-- Breadcrumbs -->
                <div class="flex items-center gap-2">
                    <div class="h-3.5 w-14 rounded shimmer-bg"></div>
                    <div class="h-2.5 w-2.5 rounded-full" style="background:#E2E8F0;"></div>
                    <div class="h-3.5 w-20 rounded shimmer-bg"></div>
                </div>
                <!-- Title -->
                <div class="h-7 w-44 md:w-56 rounded-lg shimmer-bg"></div>
            </div>
            <!-- Header Action -->
            <div class="h-9 w-28 rounded-lg shimmer-bg self-start md:self-auto"></div>
        </div>

        <!-- Stats Row Skeleton -->
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div class="p-5 bg-white border rounded-xl space-y-3 shadow-sm" style="border-color:#E2E8F0;">
                <div class="h-3.5 w-16 rounded shimmer-bg"></div>
                <div class="h-7 w-20 rounded-lg shimmer-bg"></div>
                <div class="h-3 w-28 rounded shimmer-bg"></div>
            </div>
            <div class="p-5 bg-white border rounded-xl space-y-3 shadow-sm" style="border-color:#E2E8F0;">
                <div class="h-3.5 w-20 rounded shimmer-bg"></div>
                <div class="h-7 w-14 rounded-lg shimmer-bg"></div>
                <div class="h-3 w-24 rounded shimmer-bg"></div>
            </div>
            <div class="p-5 bg-white border rounded-xl space-y-3 shadow-sm sm:col-span-2 lg:col-span-1" style="border-color:#E2E8F0;">
                <div class="h-3.5 w-14 rounded shimmer-bg"></div>
                <div class="h-7 w-24 rounded-lg shimmer-bg"></div>
                <div class="h-3 w-32 rounded shimmer-bg"></div>
            </div>
        </div>

        <!-- Content Health / Widget Cards Skeleton -->
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="p-5 bg-white border rounded-xl space-y-4 shadow-sm" style="border-color:#E2E8F0;">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-lg shimmer-bg"></div>
                    <div class="h-4 w-32 rounded shimmer-bg"></div>
                </div>
                <div class="space-y-2">
                    <div class="h-3 w-full rounded shimmer-bg"></div>
                    <div class="h-3 w-4/5 rounded shimmer-bg"></div>
                    <div class="h-3 w-3/5 rounded shimmer-bg"></div>
                </div>
            </div>
            <div class="p-5 bg-white border rounded-xl space-y-4 shadow-sm" style="border-color:#E2E8F0;">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-lg shimmer-bg"></div>
                    <div class="h-4 w-28 rounded shimmer-bg"></div>
                </div>
                <div class="space-y-2">
                    <div class="h-3 w-full rounded shimmer-bg"></div>
                    <div class="h-3 w-3/4 rounded shimmer-bg"></div>
                    <div class="h-3 w-2/3 rounded shimmer-bg"></div>
                </div>
            </div>
        </div>

        <!-- Table Skeleton -->
        <div class="bg-white border rounded-xl shadow-sm overflow-hidden" style="border-color:#E2E8F0;">
            <!-- Search & Filters Bar -->
            <div class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3" style="border-bottom:1px solid #E2E8F0;">
                <div class="h-9 w-full sm:w-56 rounded-lg shimmer-bg"></div>
                <div class="flex items-center gap-2.5">
                    <div class="h-9 w-20 rounded-lg shimmer-bg"></div>
                    <div class="h-9 w-9 rounded-lg shimmer-bg"></div>
                </div>
            </div>
            <!-- Table Header -->
            <div class="px-5 py-3 hidden sm:grid grid-cols-5 gap-4" style="background:#FBFCFE; border-bottom:1px solid #E2E8F0;">
                <div class="h-3 w-12 rounded shimmer-bg"></div>
                <div class="h-3 w-16 rounded shimmer-bg"></div>
                <div class="h-3 w-14 rounded shimmer-bg"></div>
                <div class="h-3 w-18 rounded shimmer-bg"></div>
                <div class="h-3 w-10 rounded shimmer-bg"></div>
            </div>
            <!-- Table Rows -->
            <div class="divide-y" style="--tw-divide-opacity:1; --tw-divide-color:#F1F5F9;">
                <div class="px-5 py-3.5 grid grid-cols-1 sm:grid-cols-5 gap-3">
                    <div class="h-4 w-3/4 rounded shimmer-bg"></div>
                    <div class="h-4 w-1/2 rounded shimmer-bg"></div>
                    <div class="h-4 w-2/3 rounded shimmer-bg"></div>
                    <div class="h-4 w-4/5 rounded shimmer-bg"></div>
                    <div class="h-4 w-1/3 rounded shimmer-bg"></div>
                </div>
                <div class="px-5 py-3.5 grid grid-cols-1 sm:grid-cols-5 gap-3">
                    <div class="h-4 w-2/3 rounded shimmer-bg"></div>
                    <div class="h-4 w-3/4 rounded shimmer-bg"></div>
                    <div class="h-4 w-1/2 rounded shimmer-bg"></div>
                    <div class="h-4 w-3/5 rounded shimmer-bg"></div>
                    <div class="h-4 w-1/4 rounded shimmer-bg"></div>
                </div>
                <div class="px-5 py-3.5 grid grid-cols-1 sm:grid-cols-5 gap-3">
                    <div class="h-4 w-4/5 rounded shimmer-bg"></div>
                    <div class="h-4 w-1/2 rounded shimmer-bg"></div>
                    <div class="h-4 w-3/4 rounded shimmer-bg"></div>
                    <div class="h-4 w-2/3 rounded shimmer-bg"></div>
                    <div class="h-4 w-2/5 rounded shimmer-bg"></div>
                </div>
                <div class="px-5 py-3.5 grid grid-cols-1 sm:grid-cols-5 gap-3">
                    <div class="h-4 w-1/2 rounded shimmer-bg"></div>
                    <div class="h-4 w-2/3 rounded shimmer-bg"></div>
                    <div class="h-4 w-3/5 rounded shimmer-bg"></div>
                    <div class="h-4 w-3/4 rounded shimmer-bg"></div>
                    <div class="h-4 w-1/3 rounded shimmer-bg"></div>
                </div>
                <div class="px-5 py-3.5 grid grid-cols-1 sm:grid-cols-5 gap-3">
                    <div class="h-4 w-3/5 rounded shimmer-bg"></div>
                    <div class="h-4 w-1/2 rounded shimmer-bg"></div>
                    <div class="h-4 w-2/3 rounded shimmer-bg"></div>
                    <div class="h-4 w-1/2 rounded shimmer-bg"></div>
                    <div class="h-4 w-2/5 rounded shimmer-bg"></div>
                </div>
            </div>
            <!-- Pagination -->
            <div class="px-5 py-3 flex items-center justify-between" style="border-top:1px solid #E2E8F0;">
                <div class="h-3 w-28 rounded shimmer-bg"></div>
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded shimmer-bg"></div>
                    <div class="h-8 w-8 rounded shimmer-bg"></div>
                    <div class="h-8 w-8 rounded shimmer-bg"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    /* Hide real content while skeleton is showing */
    .fi-main-loading > *:not(.fi-spa-skeleton-instance) {
        display: none !important;
    }

    /* Shimmer base + animation */
    .shimmer-bg {
        position: relative;
        overflow: hidden;
        background-color: #EEF2F7;
    }

    .shimmer-bg::after {
        position: absolute;
        inset: 0;
        transform: translateX(-100%);
        background-image: linear-gradient(
            90deg,
            rgba(255, 255, 255, 0) 0%,
            rgba(255, 255, 255, 0.55) 40%,
            rgba(255, 255, 255, 0.75) 60%,
            rgba(255, 255, 255, 0) 100%
        );
        animation: shimmer-sweep 1.3s infinite ease-in-out;
        content: '';
    }

    @keyframes shimmer-sweep {
        100% { transform: translateX(100%); }
    }

    @keyframes fadeUpIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: none; }
    }

    .animate-fade-in {
        animation: fadeUpIn 0.22s ease-out forwards;
    }

    @media (prefers-reduced-motion: reduce) {
        .shimmer-bg::after { animation: none; }
        .animate-fade-in { animation: none; opacity: 1; }
    }
</style>

<script>
    (function () {
        let skeletonTimeout = null;

        // SPA page navigation → show skeleton after 80ms (fast enough to feel instant
        // on quick loads, but catches anything over ~100ms)
        document.addEventListener('livewire:navigating', function () {
            if (skeletonTimeout) clearTimeout(skeletonTimeout);

            skeletonTimeout = setTimeout(function () {
                const mainContent = document.querySelector('.fi-main');
                if (mainContent && !mainContent.querySelector('.fi-spa-skeleton-instance')) {
                    const tpl = document.getElementById('filament-spa-skeleton-template');
                    if (tpl) {
                        mainContent.appendChild(tpl.content.cloneNode(true));
                    }
                    mainContent.classList.add('fi-main-loading');
                }
            }, 80);
        });

        // Navigation complete → tear down skeleton
        document.addEventListener('livewire:navigated', function () {
            if (skeletonTimeout) {
                clearTimeout(skeletonTimeout);
                skeletonTimeout = null;
            }

            const mainContent = document.querySelector('.fi-main');
            if (mainContent) {
                mainContent.classList.remove('fi-main-loading');
                mainContent.querySelectorAll('.fi-spa-skeleton-instance').forEach(function (el) {
                    el.remove();
                });
            }
        });

        // Initial full-page load (login → dashboard redirect or first visit):
        // Show skeleton immediately while Livewire components hydrate
        document.addEventListener('DOMContentLoaded', function () {
            const mainContent = document.querySelector('.fi-main');
            if (!mainContent) return; // Not on an admin panel page

            // Only show if there's no real widget/table content yet
            const hasRealContent = mainContent.querySelector('.fi-wi, .fi-ta, .fi-fo, .fi-header');
            if (!hasRealContent && !mainContent.querySelector('.fi-spa-skeleton-instance')) {
                const tpl = document.getElementById('filament-spa-skeleton-template');
                if (tpl) {
                    mainContent.appendChild(tpl.content.cloneNode(true));
                    mainContent.classList.add('fi-main-loading');
                }
            }
        });

        // When Livewire finishes hydrating on initial load, remove skeleton
        document.addEventListener('livewire:init', function () {
            // Give a small tick for Livewire to render widgets
            requestAnimationFrame(function () {
                setTimeout(function () {
                    const mainContent = document.querySelector('.fi-main');
                    if (mainContent) {
                        mainContent.classList.remove('fi-main-loading');
                        mainContent.querySelectorAll('.fi-spa-skeleton-instance').forEach(function (el) {
                            el.remove();
                        });
                    }
                }, 100);
            });
        });
    })();
</script>
