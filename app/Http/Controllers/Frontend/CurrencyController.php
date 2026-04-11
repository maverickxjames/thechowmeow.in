<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function switch(string $currency)
    {
        $currency = strtoupper($currency);
        
        if (in_array($currency, ['INR', 'USD'])) {
            // Only allow switching to USD if it's explicitly enabled in admin settings
            if ($currency === 'USD' && !config('petwear.enable_usd', false)) {
                return back()->with('error', 'International pricing is currently disabled.');
            }
            session(['currency' => $currency]);
            return back()->with('success', "Currency switched to {$currency}");
        }

        return back()->with('error', 'Invalid currency selection.');
    }
}
