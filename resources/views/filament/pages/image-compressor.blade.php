<x-filament-panels::page>
    {{-- Status Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">

        {{-- Compression Status --}}
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04);">
            <div style="font-size: 0.6875rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; color: #64748b; margin-bottom: 0.5rem;">
                Status
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                @if($compressionEnabled)
                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #22c55e;"></span>
                    <span style="font-size: 1.125rem; font-weight: 700; color: #0f172a;">Enabled</span>
                @else
                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #ef4444;"></span>
                    <span style="font-size: 1.125rem; font-weight: 700; color: #0f172a;">Disabled</span>
                @endif
            </div>
            <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.375rem;">
                Toggle in Site Settings → Images group
            </p>
        </div>

        {{-- Quality Level --}}
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04);">
            <div style="font-size: 0.6875rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; color: #64748b; margin-bottom: 0.5rem;">
                Quality Level
            </div>
            <div style="font-size: 1.875rem; font-weight: 800; color: #0f172a; line-height: 1.1;">
                {{ $quality }}%
            </div>
            <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.375rem;">
                Higher = better quality, larger files
            </p>
        </div>

        {{-- Compression Info --}}
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04);">
            <div style="font-size: 0.6875rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; color: #64748b; margin-bottom: 0.5rem;">
                Recommended
            </div>
            <div style="font-size: 1.125rem; font-weight: 700; color: #0f172a;">
                75 – 85%
            </div>
            <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.375rem;">
                Best balance of quality and file size
            </p>
        </div>
    </div>

    {{-- Results Section (only shown after running) --}}
    @if($hasRun)
        <div style="background: linear-gradient(135deg, #fff7ed 0%, #ffffff 100%); border: 1px solid #fed7aa; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <x-heroicon-o-check-circle style="width: 1.25rem; height: 1.25rem; color: #22c55e;" />
                Compression Results
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem;">
                <div>
                    <div style="font-size: 0.6875rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; color: #64748b;">Images Compressed</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #22c55e;">{{ $compressed }}</div>
                </div>
                <div>
                    <div style="font-size: 0.6875rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; color: #64748b;">Space Saved</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #E4652E;">{{ $this->formatBytes($bytesSaved) }}</div>
                </div>
                <div>
                    <div style="font-size: 0.6875rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; color: #64748b;">Already Optimised</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #64748b;">{{ $skipped }}</div>
                </div>
            </div>
        </div>
    @endif

    {{-- How It Works --}}
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04);">
        <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem;">How It Works</h3>
        <div style="font-size: 0.8125rem; color: #334155; line-height: 1.75;">
            <p style="margin-bottom: 0.5rem;">
                <strong>🔄 Auto-Compress on Upload:</strong> Every image uploaded through the admin panel is automatically compressed at the configured quality level before saving.
            </p>
            <p style="margin-bottom: 0.5rem;">
                <strong>📦 Bulk Compress:</strong> Click the "Compress All Images" button above to scan and compress all existing uploaded images across every section (Hero Slides, Products, Categories, Logos, Promotions, Stores, Certificates).
            </p>
            <p style="margin-bottom: 0.5rem;">
                <strong>🛡️ Safe:</strong> Images smaller than 10 KB are skipped. If compression would make a file larger, the original is kept. PDF files are never touched.
            </p>
            <p>
                <strong>⚙️ Settings:</strong> Change the quality level or disable compression entirely from <strong>Site Settings</strong> in the sidebar.
            </p>
        </div>
    </div>
</x-filament-panels::page>
