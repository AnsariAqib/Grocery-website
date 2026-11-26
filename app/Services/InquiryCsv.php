<?php

namespace App\Services;

use App\Models\Inquiry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Inquiries in and out of a spreadsheet.
 *
 * Export is the everyday half — the sales desk works its leads in Excel. Import
 * is for the other direction: leads collected off the website (a WhatsApp
 * export, an old sheet, a trade-show list) landing in the same inbox as the
 * ones the site captured.
 *
 * Both halves use one column list so a file that came out of the panel can go
 * straight back into it.
 */
class InquiryCsv
{
    /**
     * Sheet headings mapped to model fields, in the order they are written.
     *
     * Headings are matched loosely on import (see {@see self::normalise()}),
     * so capitals, spaces and underscores do not matter.
     */
    public const COLUMNS = [
        'Name' => 'name',
        'Phone' => 'phone',
        'Email' => 'email',
        'Company' => 'company',
        'Industry' => 'industry',
        'Inquiry Type' => 'inquiry_type',
        'Product' => 'product_name',
        'Category' => 'category',
        'Message' => 'message',
        'Submitted At' => 'created_at',
    ];

    /** A row without a name and a phone is not a lead. */
    public const REQUIRED = ['Name', 'Phone'];

    /**
     * Stream the given inquiries as a CSV download.
     *
     * Streamed rather than built in memory: a year of leads is a big array, and
     * the panel's PHP worker has the whole request to hold it otherwise.
     *
     * @param  Collection<int, Inquiry>|null  $records  null exports everything
     */
    public function download(?Collection $records = null): StreamedResponse
    {
        $filename = 'inquiries-'.now()->format('Y-m-d-Hi').'.csv';

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');

            // Excel reads a bare UTF-8 file as Windows-1252 and mangles ₹ and
            // any non-ASCII name; the BOM is what tells it otherwise.
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, array_keys(self::COLUMNS), ',', '"', '');

            $write = function (Inquiry $inquiry) use ($handle) {
                fputcsv($handle, array_map(
                    fn (string $field) => $field === 'created_at'
                        ? $inquiry->created_at?->format('Y-m-d H:i:s')
                        : (string) ($inquiry->{$field} ?? ''),
                    array_values(self::COLUMNS)
                ), ',', '"', '');
            };

            if ($records !== null) {
                $records->each($write);
            } else {
                Inquiry::query()->orderByDesc('created_at')->chunk(500, fn ($chunk) => $chunk->each($write));
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * A one-row sheet showing the expected headings.
     */
    public function template(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, array_keys(self::COLUMNS), ',', '"', '');
            fputcsv($handle, [
                'Ramesh Patel', '9876543210', 'ramesh@example.com', 'Patel Traders',
                'Retail', 'b2b', 'Fortune Sunlite Sunflower Oil (5L)', 'grocery',
                'Need a quotation for 50 cartons.', now()->format('Y-m-d H:i:s'),
            ], ',', '"', '');
            fclose($handle);
        }, 'inquiries-template.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Read a CSV of leads into the inquiries table.
     *
     * A lead already in the table — same phone, same name — is left alone
     * rather than duplicated, so re-importing a sheet is safe.
     *
     * @return array{imported: int, skipped: int, duplicates: int, errors: array<int, string>}
     */
    public function import(string $path): array
    {
        $result = ['imported' => 0, 'skipped' => 0, 'duplicates' => 0, 'errors' => []];

        $handle = fopen($path, 'r');

        if ($handle === false) {
            $result['errors'][] = 'The file could not be opened.';

            return $result;
        }

        $headings = fgetcsv($handle, 0, ',', '"', '');

        if ($headings === false || $headings === null) {
            fclose($handle);
            $result['errors'][] = 'The file is empty.';

            return $result;
        }

        // Strip the BOM off the first heading, or "Name" never matches.
        $headings[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $headings[0]);

        $map = $this->headingMap($headings);

        foreach (self::REQUIRED as $required) {
            if (! in_array(self::COLUMNS[$required], $map, true)) {
                $result['errors'][] = "The sheet needs a \"{$required}\" column.";
            }
        }

        if ($result['errors'] !== []) {
            fclose($handle);

            return $result;
        }

        $line = 1;

        while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
            $line++;

            // Trailing blank lines are normal in a hand-edited sheet.
            if ($row === [null] || $row === ['']) {
                continue;
            }

            $values = $this->rowValues($row, $map);

            if ($values['name'] === '' || $values['phone'] === '') {
                $result['skipped']++;
                $result['errors'][] = "Row {$line}: needs both a name and a phone number.";

                continue;
            }

            $exists = Inquiry::query()
                ->where('phone', $values['phone'])
                ->where('name', $values['name'])
                ->exists();

            if ($exists) {
                $result['duplicates']++;

                continue;
            }

            $inquiry = Inquiry::create([
                'name' => $values['name'],
                'phone' => $values['phone'],
                'email' => $values['email'] ?: null,
                'company' => $values['company'] ?: null,
                'industry' => $values['industry'] ?: null,
                'inquiry_type' => $this->inquiryType($values['inquiry_type']),
                'product_name' => $values['product_name'] ?: null,
                'category' => $values['category'] ?: null,
                'message' => $values['message'] ?: null,
            ]);

            // An imported lead keeps the date it was actually captured, so the
            // list stays in true chronological order.
            if ($values['created_at'] !== '' && ($when = $this->parseDate($values['created_at']))) {
                $inquiry->forceFill(['created_at' => $when])->saveQuietly();
            }

            $result['imported']++;
        }

        fclose($handle);

        return $result;
    }

    /**
     * Column index => model field, for the headings this sheet actually has.
     *
     * @param  array<int, string|null>  $headings
     * @return array<int, string>
     */
    private function headingMap(array $headings): array
    {
        $known = [];
        foreach (self::COLUMNS as $heading => $field) {
            $known[$this->normalise($heading)] = $field;
        }

        $map = [];
        foreach ($headings as $index => $heading) {
            $key = $this->normalise((string) $heading);
            if (isset($known[$key])) {
                $map[$index] = $known[$key];
            }
        }

        return $map;
    }

    /**
     * Every field the columns can carry, defaulted to '' so callers never have
     * to check whether a column was present.
     *
     * @param  array<int, string|null>  $row
     * @param  array<int, string>  $map
     * @return array<string, string>
     */
    private function rowValues(array $row, array $map): array
    {
        $values = array_fill_keys(array_values(self::COLUMNS), '');

        foreach ($map as $index => $field) {
            // Same reason as ProductImporter::utf8() — a sheet saved by Excel
            // on Windows carries bytes MySQL rejects, and one of them would
            // otherwise sink the row it is in.
            $values[$field] = trim(ProductImporter::utf8((string) ($row[$index] ?? '')));
        }

        return $values;
    }

    /**
     * The storefront only ever writes these three, and the badge colours in the
     * table are keyed on them.
     */
    private function inquiryType(string $value): string
    {
        $value = strtolower(trim($value));

        return match (true) {
            str_contains($value, 'b2b'), str_contains($value, 'business'), str_contains($value, 'bulk') => 'b2b',
            str_contains($value, 'dealer'), str_contains($value, 'brand') => 'dealership',
            default => 'retail',
        };
    }

    private function parseDate(string $value): ?Carbon
    {
        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalise(string $heading): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower($heading)) ?? '';
    }
}
