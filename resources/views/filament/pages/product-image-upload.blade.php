<x-filament-panels::page>
    {{-- Counts --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">

        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04);">
            <div style="font-size: 0.6875rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; color: #64748b; margin-bottom: 0.5rem;">
                Photos on the site
            </div>
            <div style="font-size: 1.875rem; font-weight: 800; color: #0f172a; line-height: 1.1;">
                {{ number_format($this->imageCount()) }}
            </div>
            <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.375rem;">
                In the product photo folder
            </p>
        </div>

        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04);">
            <div style="font-size: 0.6875rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; color: #64748b; margin-bottom: 0.5rem;">
                Last batch
            </div>
            <div style="font-size: 1.875rem; font-weight: 800; color: {{ $lastBatch > 0 ? '#22c55e' : '#94a3b8' }}; line-height: 1.1;">
                {{ $lastBatch }}
            </div>
            <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.375rem;">
                @if ($bytesSaved > 0)
                    Compression saved {{ $this->formatBytes($bytesSaved) }}
                @else
                    Uploaded in this session
                @endif
            </p>
        </div>
    </div>

    {{-- The uploader --}}
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04); margin-bottom: 1.5rem;">
        <form wire:submit="finish">
            {{ $this->form }}

            <div style="margin-top: 1.25rem; display: flex; justify-content: flex-end;">
                <x-filament::button type="submit" icon="heroicon-o-check">
                    Compress &amp; clear
                </x-filament::button>
            </div>
        </form>
    </div>

    {{-- The gallery, so a photo can be seen next to the name the sheet uses --}}
    @php $gallery = $this->recentImages(); @endphp

    @if (count($gallery) > 0)
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04); margin-bottom: 1.5rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">On the site</h3>
            <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 1rem;">
                Newest first. The name under each photo is what the import sheet has to point at.
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 1rem;">
                @foreach ($gallery as $photo)
                    <div>
                        <div style="aspect-ratio: 1; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                            <img
                                src="{{ $photo['url'] }}"
                                alt="{{ $photo['name'] }}"
                                loading="lazy"
                                style="max-width: 100%; max-height: 100%; object-fit: contain;"
                            >
                        </div>
                        <div
                            title="{{ $photo['name'] }}"
                            style="font-family: ui-monospace, monospace; font-size: 0.6875rem; color: #475569; margin-top: 0.375rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                        >
                            {{ $photo['name'] }}
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($this->hasMoreImages())
                <div style="margin-top: 1.25rem; display: flex; justify-content: center; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 0.75rem; color: #64748b;">
                        Showing {{ count($gallery) }} of {{ number_format($this->imageCount()) }}
                    </span>
                    <x-filament::button
                        wire:click="loadMore"
                        color="gray"
                        size="sm"
                        icon="heroicon-o-chevron-down"
                        wire:loading.attr="disabled"
                        wire:target="loadMore"
                    >
                        Load more
                    </x-filament::button>
                </div>
            @endif
        </div>
    @endif

    {{-- How it works --}}
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04);">
        <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem;">How It Works</h3>
        <div style="font-size: 0.8125rem; color: #334155; line-height: 1.75;">
            <p style="margin-bottom: 0.5rem;">
                <strong>📤 Drop everything at once:</strong> Select or drag a whole folder of product photos. Each one uploads on its own, so a large batch will not time out.
            </p>
            <p style="margin-bottom: 0.5rem;">
                <strong>🏷️ Names are kept:</strong> A photo stays under its own filename, tidied to lowercase with hyphens — <code>Dettol 500ML.JPG</code> is stored as <code>dettol-500ml.jpg</code>. The import sheet is read the same way, so both sides still meet.
            </p>
            <p style="margin-bottom: 0.5rem;">
                <strong>♻️ Same name replaces:</strong> Uploading a photo whose name is already here overwrites it. That is how you correct a wrong picture without touching any product.
            </p>
            <p>
                <strong>🗜️ Compression:</strong> "Compress & clear" squeezes the batch you just dropped and empties the field for the next one. Nothing is deleted from the site.
            </p>
        </div>
    </div>
</x-filament-panels::page>
