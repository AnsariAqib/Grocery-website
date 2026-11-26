@php
    $healthy = ($status['reachable'] ?? false) && ($status['resolves_to_target'] ?? false);
@endphp

<x-filament-panels::page>
    {{-- Verdict --}}
    <div style="background: {{ $healthy ? 'linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%)' : 'linear-gradient(135deg, #fef2f2 0%, #ffffff 100%)' }}; border: 1px solid {{ $healthy ? '#bbf7d0' : '#fecaca' }}; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.5rem;">
            <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: {{ $healthy ? '#22c55e' : '#ef4444' }};"></span>
            <h3 style="font-size: 1.125rem; font-weight: 800; color: #0f172a;">
                {{ $healthy ? 'Uploaded images are being served correctly' : 'Uploaded images cannot be served' }}
            </h3>
        </div>
        <p style="font-size: 0.8125rem; color: #334155; line-height: 1.7;">
            @if($healthy)
                <code>public/storage</code> points at <code>storage/app/public</code>, and a test file written to the disk was readable through it. New uploads will load on the site.
            @elseif($status['is_real_directory'] ?? false)
                <strong>public/storage is a real folder, not a link.</strong> Uploads are saved into <code>storage/app/public/</code> but the web server looks for them in <code>public/storage/</code>, so every new image returns 404. Press <strong>Repair Storage Link</strong> above to fix it.
            @elseif(! ($status['exists'] ?? false))
                <strong>public/storage does not exist.</strong> The link that makes uploaded files public was never created. Press <strong>Repair Storage Link</strong> above.
            @else
                The link exists but a test file written to the disk was not readable through it. Details below.
            @endif
        </p>
    </div>

    {{-- Checks --}}
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04);">
        <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem;">Checks</h3>

        <div style="display: grid; gap: 0.5rem; font-size: 0.8125rem;">
            @php
                $checks = [
                    'Test file readable through public/storage' => $status['reachable'] ?? false,
                    'public/storage exists' => $status['exists'] ?? false,
                    'public/storage is a symlink' => $status['is_link'] ?? false,
                    'Resolves to storage/app/public' => $status['resolves_to_target'] ?? false,
                    'storage/app/public exists' => $status['target_exists'] ?? false,
                    'storage/app/public is writable' => $status['target_writable'] ?? false,
                    'public/ folder is writable' => $status['public_writable'] ?? false,
                    'PHP can create symlinks' => $status['symlink_supported'] ?? false,
                ];
            @endphp

            @foreach($checks as $label => $passed)
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.5rem 0.75rem; border-radius: 8px; background: {{ $passed ? '#f8fafc' : '#fff7ed' }};">
                    <span style="color: #334155;">{{ $label }}</span>
                    <span style="font-weight: 700; color: {{ $passed ? '#16a34a' : '#dc2626' }};">{{ $passed ? 'Yes' : 'No' }}</span>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 1.25rem; font-size: 0.75rem; color: #64748b; line-height: 1.9; word-break: break-all;">
            <div><strong>Link path:</strong> {{ $status['link_path'] ?? '—' }}</div>
            <div><strong>Should point to:</strong> {{ $status['target_path'] ?? '—' }}</div>
            <div><strong>Currently points to:</strong> {{ $status['link_target'] ?? ($status['is_real_directory'] ?? false ? 'nothing — it is a real folder' : '—') }}</div>
            <div><strong>PHP runs as:</strong> {{ $status['php_user'] ?? 'unknown' }}</div>
        </div>
    </div>

    {{-- Upload log --}}
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04);">
        <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.375rem;">Upload Log</h3>
        <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 1rem;">
            Written every time an image is saved from the admin panel. Newest entries are at the bottom.
        </p>

        @if($log === '')
            <div style="font-size: 0.8125rem; color: #64748b; background: #f8fafc; border-radius: 8px; padding: 1rem;">
                Nothing logged yet — save an image from any section, then press <strong>Re-check</strong>.
            </div>
        @else
            <pre style="font-size: 0.6875rem; line-height: 1.6; color: #0f172a; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; max-height: 460px; overflow: auto; white-space: pre-wrap; word-break: break-all;">{{ $log }}</pre>
        @endif
    </div>
</x-filament-panels::page>
