<?php

if (!function_exists('currency_format')) {
    /**
     * Format the given price based on the active session currency.
     * Prices in the DB are always in INR.
     */
    function currency_format($priceINR)
    {
        // Safety cast
        $priceINR = (float) $priceINR;
        
        $enableUsd = config('petwear.enable_usd', false);
        $activeCurrency = session('currency', 'INR');
        
        if ($enableUsd && $activeCurrency === 'USD') {
            $exchangeRate = (float) config('petwear.usd_exchange_rate', 83.50);
            if ($exchangeRate <= 0) $exchangeRate = 1; // Prevent division by zero
            
            $priceUSD = $priceINR / $exchangeRate;
            return '$' . number_format($priceUSD, 2);
        }

        // Native INR processing
        $symbol = config('petwear.currency_symbol', config('app.currency_symbol', '₹'));
        return $symbol . number_format($priceINR);
    }
}

if (!function_exists('get_active_currency')) {
    function get_active_currency()
    {
        if (!config('petwear.enable_usd', false)) {
            return 'INR';
        }
        return session('currency', 'INR');
    }
}

if (!function_exists('get_converted_amount')) {
    /**
     * Used for strictly returning mathematical conversion for integrations like Razorpay
     */
    function get_converted_amount($priceINR)
    {
        $priceINR = (float) $priceINR;
        $enableUsd = config('petwear.enable_usd', false);
        $activeCurrency = session('currency', 'INR');
        
        if ($enableUsd && $activeCurrency === 'USD') {
            $exchangeRate = (float) config('petwear.usd_exchange_rate', 83.50);
            if ($exchangeRate <= 0) $exchangeRate = 1;
            
            return round($priceINR / $exchangeRate, 2);
        }

        return $priceINR;
    }
}
