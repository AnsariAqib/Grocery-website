@php
    $preview = $this->getPreview();
@endphp

<x-filament-panels::page>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04); margin-bottom: 1.5rem;">
        {{ $this->form }}

        {{-- The counts follow the form live, so the selection is checked before
             a file is made rather than after it is opened. --}}
        <div style="margin-top: 1.5rem; display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; justify-content: space-between;">
            <div style="font-size: 0.875rem; color: #334155;">
                <strong style="color: #E4652E; font-size: 1.125rem;">{{ $preview['products'] }}</strong>
                {{ Str::plural('product', $preview['products']) }}
                across
                <strong>{{ $preview['categories'] }}</strong>
                {{ Str::plural('category', $preview['categories']) }}
                <span style="color: #94a3b8;">&nbsp;·&nbsp; {{ $preview['total'] }} live on the site in total</span>
            </div>

            <div style="display: flex; gap: 0.75rem; align-items: center;">
                <span wire:loading wire:target="download" style="font-size: 0.8125rem; color: #64748b;">
                    Setting the pages — a big catalogue takes a few seconds.
                </span>

                <x-filament::button
                    type="button"
                    wire:click="download"
                    wire:target="download"
                    wire:loading.attr="disabled"
                    icon="heroicon-o-arrow-down-tray"
                >
                    Make the PDF
                </x-filament::button>
            </div>
        </div>
    </div>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04);">
        <h3 style="margin: 0 0 0.75rem 0; font-size: 1rem; font-weight: 700; color: #061427;">What you get</h3>

        <div style="font-size: 0.875rem; color: #475569; line-height: 1.7;">
            <p style="margin-bottom: 0.5rem;">
                <strong>📄 A cover, then the range:</strong> your title and line underneath, the logo, the
                counts and the shop's address and numbers — then every category in the site's own order,
                split by variety, with the brand, pack size and price of each product.
            </p>
            <p style="margin-bottom: 0.5rem;">
                <strong>🔁 It is built from the live catalogue:</strong> whatever the site shows today is
                what prints. Change a price in Products and make the PDF again — there is no second list to
                keep in step.
            </p>
            <p style="margin-bottom: 0.5rem;">
                <strong>💰 Prices follow the product:</strong> a product with pack sizes prints its first
                size, and the rest are listed under the name. Anything without a rate prints
                <em>On request</em>, exactly as the website says it.
            </p>
            <p style="margin-bottom: 0;">
                <strong>🔗 Straight onto the site:</strong> tick "save it as the site's master catalogue"
                and the Download Now buttons on the storefront start serving this file — Retail or B2B
                depending on the side you picked.
            </p>
        </div>
    </div>

</x-filament-panels::page>
