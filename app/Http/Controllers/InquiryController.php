<?php

namespace App\Http\Controllers;

use App\Http\Requests\InquiryRequest;
use App\Models\Inquiry;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class InquiryController extends Controller
{
    /**
     * Store a new quick contact or bulk inquiry submission.
     */
    public function store(InquiryRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        // If product_name is empty, attempt to extract it from the message if formatted as "Product: ..."
        if (empty($validated['product_name']) && ! empty($validated['message'])) {
            if (preg_match('/^Product:\s*(.+)$/m', $validated['message'], $matches)) {
                $validated['product_name'] = trim($matches[1]);
            }
        }

        // Save the inquiry to the database
        $inquiry = Inquiry::create($validated);

        // Fetch WhatsApp settings
        $whatsappNumber = Setting::val('whatsapp_number', '919879197739');

        // Build custom WhatsApp pre-filled text
        $text = "Hello Jay Tulsi Agency,\n\n"
              ."I have submitted an inquiry on your website:\n\n"
              .'*Name:* '.$validated['name']."\n"
              .'*Phone:* '.$validated['phone']."\n";

        if (! empty($validated['email'])) {
            $text .= '*Email:* '.$validated['email']."\n";
        }
        if (! empty($validated['company'])) {
            $text .= '*Company:* '.$validated['company']."\n";
        }
        if (! empty($validated['industry'])) {
            $text .= '*Industry:* '.$validated['industry']."\n";
        }
        if (! empty($validated['inquiry_type'])) {
            $text .= '*Inquiry Type:* '.ucfirst($validated['inquiry_type'])."\n";
        }
        if (! empty($validated['product_name'])) {
            $text .= '*Product Inquired:* '.$validated['product_name']."\n";
        }
        if (! empty($validated['category'])) {
            $text .= '*Category:* '.ucfirst(str_replace('-', ' ', $validated['category']))."\n";
        }
        if (! empty($validated['message'])) {
            $text .= '*Message:* '.$validated['message']."\n";
        }

        // Clean WhatsApp number
        $cleanNumber = preg_replace('/\D/', '', $whatsappNumber);
        $whatsappUrl = 'https://api.whatsapp.com/send?phone='.$cleanNumber.'&text='.urlencode($text);

        $companyText = ! empty($validated['company']) ? " ({$validated['company']})" : '';
        $message = "Thank you, {$validated['name']}{$companyText}! Your inquiry has been received. Redirecting to WhatsApp...";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'whatsapp_url' => $whatsappUrl,
                'data' => $inquiry,
            ]);
        }

        return redirect()->away($whatsappUrl);
    }
}
