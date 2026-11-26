@php
    $result = $this->result;
    $hasSheet = $this->sheetName !== null;
@endphp

<x-filament-panels::page>

    {{-- The uploader.

         Both buttons sit outside the form and are plain wire:click calls. An
         earlier version put "Import now" inside a wire:submit form and gated it
         on the preview having run; between the form's own submit and the
         loading state toggling the same `disabled` attribute, the click did not
         always reach the server, and a sheet the admin thought was imported was
         not. Neither button is ever disabled now, and pressing "Import now"
         first simply imports — the check is a convenience, not a gate. --}}
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04); margin-bottom: 1.5rem;">
        {{ $this->form }}

        <div style="margin-top: 1.25rem; display: flex; flex-wrap: wrap; gap: 0.75rem; justify-content: flex-end; align-items: center;">
            <span wire:loading wire:target="checkSheet,startImport" style="font-size: 0.8125rem; color: #64748b;">
                Working through the sheet — this can take a minute on a big catalogue.
            </span>

            <x-filament::button
                type="button"
                wire:click="checkSheet"
                wire:target="checkSheet"
                wire:loading.attr="disabled"
                color="gray"
                icon="heroicon-o-magnifying-glass"
            >
                Check sheet
            </x-filament::button>

            <x-filament::button
                type="button"
                wire:click="startImport"
                wire:target="startImport"
                wire:loading.attr="disabled"
                icon="heroicon-o-arrow-down-tray"
            >
                Import now
            </x-filament::button>
        </div>

        <p style="margin-top: 0.75rem; font-size: 0.75rem; color: #64748b; text-align: right;">
            @if ($hasSheet)
                <strong>{{ $this->sheetName }}</strong> is ready.
                "Check sheet" writes nothing — it only reports. "Import now" is what saves.
            @else
                Choose a sheet first. "Check sheet" then reports what it would do without
                writing anything; "Import now" is what saves.
            @endif
        </p>
    </div>

    @if (filled($result))
        {{-- What the run did, or would do --}}
        <div style="background: {{ $this->committed ? '#f0fdf4' : '#fffbeb' }}; border: 1px solid {{ $this->committed ? '#bbf7d0' : '#fde68a' }}; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1rem; font-size: 0.8125rem; color: #334155;">
            @if ($this->committed)
                <strong>Imported.</strong> {{ number_format($result['rows']) }} rows became
                {{ number_format($result['groups']) }} products. These are live on the site now.
            @else
                <strong>Nothing has been written yet.</strong> This is what
                {{ number_format($result['rows']) }} rows would do. Press "Import now" to keep it.
            @endif
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            @foreach ([
                ['Products created', $result['productsCreated'], '#22c55e'],
                ['Products updated', $result['productsUpdated'], '#0f172a'],
                ['Pack sizes', $result['variants'], '#0f172a'],
                ['Rows skipped', $result['rowsSkipped'], $result['rowsSkipped'] > 0 ? '#f59e0b' : '#94a3b8'],
            ] as [$label, $value, $colour])
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04);">
                    <div style="font-size: 0.6875rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; color: #64748b; margin-bottom: 0.5rem;">
                        {{ $label }}
                    </div>
                    <div style="font-size: 1.875rem; font-weight: 800; color: {{ $colour }}; line-height: 1.1;">
                        {{ number_format($value) }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Which products, not just how many --}}
        @if (filled($result['products']))
            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04); margin-bottom: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem;">
                    {{ $this->committed ? 'Products imported' : 'Products this would import' }}
                </h3>

                <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                    @foreach ($result['products'] as $i => $product)
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 0.875rem; {{ $i > 0 ? 'border-top: 1px solid #f1f5f9;' : '' }} {{ $i % 2 ? 'background: #fafafa;' : '' }}">
                            <span style="flex: 0 0 auto; font-size: 0.625rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; border-radius: 4px; padding: 0.125rem 0.375rem; {{ $product['created'] ? 'background: #dcfce7; color: #15803d;' : 'background: #e2e8f0; color: #475569;' }}">
                                {{ $product['created'] ? 'New' : 'Updated' }}
                            </span>

                            <span style="flex: 1 1 auto; font-size: 0.8125rem; font-weight: 600; color: #0f172a;">
                                {{ $product['name'] }}
                            </span>

                            @if ($product['sizes'] > 0)
                                <span style="flex: 0 0 auto; font-size: 0.6875rem; color: #64748b;">
                                    {{ $product['sizes'] }} sizes
                                </span>
                            @endif

                            @if ($product['sku'])
                                <span style="flex: 0 0 auto; font-family: ui-monospace, monospace; font-size: 0.6875rem; color: #94a3b8;">
                                    {{ $product['sku'] }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if ($result['groups'] > count($result['products']))
                    <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.75rem;">
                        Showing the first {{ count($result['products']) }} of
                        {{ number_format($result['groups']) }}.
                    </p>
                @endif

                @if ($this->committed)
                    <p style="font-size: 0.8125rem; color: #64748b; margin-top: 0.875rem;">
                        <a href="{{ \App\Filament\Resources\Products\ProductResource::getUrl() }}" style="color: #FA5400; font-weight: 600;">Open Products</a>
                        to check them — the search box finds them by SKU too.
                    </p>
                @endif
            </div>
        @endif

        {{-- New categories: these land bare and need artwork --}}
        @if (filled($result['createdCategories']))
            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04); margin-bottom: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">
                    {{ count($result['createdCategories']) }} new
                    {{ str('category')->plural(count($result['createdCategories'])) }}
                </h3>
                <p style="font-size: 0.8125rem; color: #64748b; margin-bottom: 0.75rem;">
                    Give each one an image and an icon under Catalogue → Categories, or it will look
                    bare on the category pages.
                </p>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    @foreach ($result['createdCategories'] as $name)
                        <span style="font-size: 0.75rem; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.25rem 0.5rem; color: #334155;">
                            {{ $name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Photos the sheet names but nobody uploaded --}}
        @if (filled($result['missingImages']))
            <div style="background: #fff; border: 1px solid #fecaca; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04); margin-bottom: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700; color: #b91c1c; margin-bottom: 0.5rem;">
                    {{ count($result['missingImages']) }} missing
                    {{ str('photo')->plural(count($result['missingImages'])) }}
                </h3>
                <p style="font-size: 0.8125rem; color: #64748b; margin-bottom: 0.75rem;">
                    The sheet points at these but they are not on the site. Upload them on
                    <a href="{{ \App\Filament\Pages\ProductImageUploadPage::getUrl() }}" style="color: #FA5400; font-weight: 600;">Bulk Image Upload</a>,
                    then run this import again — products match on SKU, so nothing is duplicated.
                </p>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    @foreach (array_slice($result['missingImages'], 0, 40) as $filename)
                        <span style="font-family: ui-monospace, monospace; font-size: 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 0.25rem 0.5rem; color: #991b1b;">
                            {{ $filename }}
                        </span>
                    @endforeach
                </div>
                @if (count($result['missingImages']) > 40)
                    <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.75rem;">
                        … and {{ count($result['missingImages']) - 40 }} more.
                    </p>
                @endif
            </div>
        @endif

        {{-- Rows that could not be used --}}
        @if (filled($result['warnings']))
            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04); margin-bottom: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem;">
                    {{ count($result['warnings']) }}
                    {{ str('row')->plural(count($result['warnings'])) }} had problems
                </h3>
                <ul style="font-size: 0.8125rem; color: #334155; line-height: 1.7; padding-left: 1.1rem; list-style: disc;">
                    @foreach (array_slice($result['warnings'], 0, 25) as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
                @if (count($result['warnings']) > 25)
                    <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.75rem;">
                        … and {{ count($result['warnings']) - 25 }} more.
                    </p>
                @endif
            </div>
        @endif
    @endif

    {{-- How it works --}}
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04);">
        <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem;">How It Works</h3>
        <div style="font-size: 0.8125rem; color: #334155; line-height: 1.75;">
            <p style="margin-bottom: 0.5rem;">
                <strong>🖼️ Photos go up first:</strong> The sheet only names its photos, it does not carry
                them. Upload them on
                <a href="{{ \App\Filament\Pages\ProductImageUploadPage::getUrl() }}" style="color: #FA5400; font-weight: 600;">Bulk Image Upload</a>
                before importing, or products arrive without pictures.
            </p>
            <p style="margin-bottom: 0.5rem;">
                <strong>🔗 Pack sizes group by Parent SKU:</strong> Rows sharing a Parent SKU become one
                product with size chips. The row whose SKU equals its own Parent SKU is the one that
                describes the product.
            </p>
            <p style="margin-bottom: 0.5rem;">
                <strong>♻️ Re-importing is safe:</strong> Products are matched on SKU — or, for a sheet
                without codes, on the product name inside the same subcategory — so running the same
                sheet twice updates rather than duplicates. Fix the sheet and import again.
            </p>
            <p style="margin-bottom: 0.5rem;">
                <strong>📤 Editing what is already here:</strong> "Export catalogue" at the top of this
                page downloads every product in exactly this format. Open it in Excel, change what you
                need — prices, badges, descriptions — and import the same file back.
            </p>
            <p style="margin-bottom: 0.5rem;">
                <strong>🛟 All or nothing:</strong> The whole sheet is written in one transaction. If
                anything fails part way, nothing at all is saved.
            </p>
            <p>
                <strong>📁 Missing categories are created:</strong> A category or subcategory named in the
                sheet that does not exist yet is made automatically — put "B2B" in the Section column to
                file it under the B2B side.
            </p>
        </div>

        <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin: 1.5rem 0 0.5rem;">Columns it reads</h3>
        <p style="font-size: 0.8125rem; color: #64748b; margin-bottom: 0.75rem;">
            Spaces and capitals in the headings do not matter. Any column not listed here is ignored.
            The <strong>bold</strong> ones are the only three a row cannot do without. "Download
            template" at the top of this page gives you a sheet with these headings already in place.
        </p>
        <div style="display: flex; flex-wrap: wrap; gap: 0.375rem;">
            @foreach (array_keys(\App\Services\ProductImporter::COLUMNS) as $heading)
                @php $required = in_array($heading, \App\Services\ProductImporter::REQUIRED, true); @endphp
                <span style="font-family: ui-monospace, monospace; font-size: 0.75rem; background: {{ $required ? '#fff7ed' : '#f1f5f9' }}; border: 1px solid {{ $required ? '#fed7aa' : '#e2e8f0' }}; border-radius: 6px; padding: 0.25rem 0.5rem; color: {{ $required ? '#9a3412' : '#334155' }}; font-weight: {{ $required ? '700' : '400' }};">
                    {{ $heading }}
                </span>
            @endforeach
        </div>
    </div>

</x-filament-panels::page>
