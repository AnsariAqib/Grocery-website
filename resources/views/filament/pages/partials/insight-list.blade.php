{{--
    One "top ten" card: a title, a line saying what is being counted, and the
    rows with a bar behind each so the shape of the list is readable at a
    glance rather than having to compare numbers.

    $title, $subtitle, $rows, $valueKey, $valueLabel, $empty, $secondKey?, $secondLabel?
--}}
@php
    $max = 0;

    foreach ($rows as $row) {
        $max = max($max, (int) $row[$valueKey]);
    }
@endphp

<div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem 1.25rem 1rem; box-shadow: 0 2px 8px rgba(6,20,39,0.04);">
    <h3 style="font-size: 0.9375rem; font-weight: 800; color: #0f172a;">{{ $title }}</h3>
    <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.125rem;">{{ $subtitle }}</p>

    @if (count($rows) === 0)
        <div style="margin-top: 0.875rem; font-size: 0.8125rem; color: #64748b; background: #f8fafc; border-radius: 8px; padding: 0.875rem 1rem;">
            {{ $empty }}
        </div>
    @else
        <div style="margin-top: 0.875rem; display: grid; gap: 0.375rem;">
            @foreach ($rows as $index => $row)
                @php
                    $value = (int) $row[$valueKey];
                    $width = $max > 0 ? max(3, round($value / $max * 100)) : 0;
                @endphp

                <div style="position: relative; border-radius: 8px; overflow: hidden; background: #f8fafc;">
                    {{-- The bar sits behind the text so the row height never depends on it. --}}
                    <div style="position: absolute; inset: 0 auto 0 0; width: {{ $width }}%; background: linear-gradient(90deg, rgba(250,84,0,0.16), rgba(250,84,0,0.07));"></div>

                    <div style="position: relative; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.5rem 0.75rem;">
                        <span style="display: flex; align-items: center; gap: 0.5rem; min-width: 0;">
                            <span style="flex: none; width: 1.125rem; font-size: 0.6875rem; font-weight: 700; color: #94a3b8;">{{ $index + 1 }}</span>
                            <span style="font-size: 0.8125rem; color: #0f172a; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $row['name'] }}">{{ $row['name'] }}</span>
                        </span>

                        <span style="flex: none; display: flex; align-items: baseline; gap: 0.5rem;">
                            @isset($secondKey)
                                <span style="font-size: 0.6875rem; color: #64748b;">{{ number_format((int) $row[$secondKey]) }} {{ $secondLabel }}</span>
                            @endisset
                            <span style="font-size: 0.8125rem; font-weight: 800; color: #b23c00;">{{ number_format($value) }}</span>
                            <span style="font-size: 0.6875rem; color: #94a3b8;">{{ $valueLabel }}</span>
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
