@extends('layouts.admin')
@section('title', 'System Settings')
@section('subtitle', 'Manage application configuration, email, and payment gateways')

@section('content')

@php
    $activeTab = session('tab', 'general');
@endphp

<div class="max-w-5xl" x-data="{ tab: '{{ $activeTab }}' }">
    
    {{-- Tabs --}}
    <div class="flex gap-2 mb-6 border-b border-gray-200">
        <button @click="tab = 'general'" 
                :class="tab === 'general' ? 'border-violet-600 text-violet-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="px-4 py-3 font-semibold text-sm border-b-2 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            General
        </button>
        <button @click="tab = 'smtp'" 
                :class="tab === 'smtp' ? 'border-violet-600 text-violet-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="px-4 py-3 font-semibold text-sm border-b-2 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            SMTP (Mail)
        </button>
        <button @click="tab = 'payment'" 
                :class="tab === 'payment' ? 'border-violet-600 text-violet-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="px-4 py-3 font-semibold text-sm border-b-2 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            Razorpay Configuration
        </button>
    </div>

    {{-- Forms --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        
        {{-- General Tab --}}
        <form action="{{ route('admin.settings.store') }}" method="POST" x-show="tab === 'general'" x-cloak>
            @csrf
            <input type="hidden" name="tab" value="general">
            <h3 class="text-lg font-bold text-gray-900 mb-5">General Information</h3>
            <div class="space-y-4 max-w-lg">
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Application Name</label>
                    <input type="text" name="app_name" value="{{ $settings['app_name'] ?? config('app.name') }}" required
                           class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Base Currency Setup <span class="text-xs text-gray-400 font-normal">(Default)</span></label>
                    <div class="flex gap-2">
                        <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] ?? '₹' }}" placeholder="Symbol (₹, $)" required
                               class="w-20 rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200 text-center">
                        <input type="text" name="currency_code" value="{{ $settings['currency_code'] ?? 'INR' }}" placeholder="Code (INR, USD)" required
                               class="flex-1 rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                    </div>
                </div>

                <div class="pt-4 mt-4 border-t border-gray-100">
                    <h4 class="text-sm font-bold text-gray-900 mb-3">Multi-Currency (USD)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700 block mb-1.5">Enable USD Pricing</label>
                            <select name="enable_usd" class="w-full rounded-lg border-gray-300 text-sm focus:border-violet-400 focus:ring-violet-200">
                                <option value="1" {{ ($settings['enable_usd'] ?? '0') == '1' ? 'selected' : '' }}>Enabled</option>
                                <option value="0" {{ ($settings['enable_usd'] ?? '0') == '0' ? 'selected' : '' }}>Disabled</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 block mb-1.5">Exchange Rate (1 USD = ? INR)</label>
                            <input type="number" step="0.01" name="usd_exchange_rate" value="{{ $settings['usd_exchange_rate'] ?? '83.50' }}"
                                   class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">When enabled, international customers can switch the view to USD in the navigation bar.</p>
                </div>

                <div class="pt-4">
                    <button type="submit" class="px-6 py-2.5 bg-violet-600 text-white rounded-lg font-semibold hover:bg-violet-700 transition-colors text-sm">Save Changes</button>
                </div>
            </div>
        </form>

        {{-- SMTP Tab --}}
        <form action="{{ route('admin.settings.store') }}" method="POST" x-show="tab === 'smtp'" x-cloak>
            @csrf
            <input type="hidden" name="tab" value="smtp">
            <h3 class="text-lg font-bold text-gray-900 mb-5">SMTP Configuration</h3>
            <p class="text-xs text-gray-500 mb-5 max-w-2xl leading-relaxed">
                Configure your SMTP server settings to ensure emails (like new order confirmations or password resets) are delivered correctly. If left blank, the application will attempt to use `.env` settings.
            </p>
            <div class="space-y-4 max-w-lg">
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">SMTP Host</label>
                    <input type="text" name="smtp_host" value="{{ $settings['smtp_host'] ?? config('mail.mailers.smtp.host') }}" placeholder="smtp.mailtrap.io"
                           class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200 font-mono">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">SMTP Port</label>
                    <input type="number" name="smtp_port" value="{{ $settings['smtp_port'] ?? config('mail.mailers.smtp.port') }}" placeholder="2525"
                           class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200 font-mono">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">SMTP Username</label>
                    <input type="text" name="smtp_username" value="{{ $settings['smtp_username'] ?? '' }}" placeholder="Username"
                           class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200 font-mono">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">SMTP Password</label>
                    <input type="password" name="smtp_password" value="{{ $settings['smtp_password'] ?? '' }}" placeholder="••••••••"
                           class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200 font-mono">
                </div>
                <div class="pt-2">
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">From Address</label>
                    <input type="email" name="smtp_from_address" value="{{ $settings['smtp_from_address'] ?? config('mail.from.address') }}" placeholder="hello@petwear.com"
                           class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                </div>
                <div class="pt-4">
                    <button type="submit" class="px-6 py-2.5 bg-violet-600 text-white rounded-lg font-semibold hover:bg-violet-700 transition-colors text-sm">Save Changes</button>
                </div>
            </div>
        </form>

        {{-- Payment Gateway Tab --}}
        <form action="{{ route('admin.settings.store') }}" method="POST" x-show="tab === 'payment'" x-cloak>
            @csrf
            <input type="hidden" name="tab" value="payment">
            <h3 class="text-lg font-bold text-gray-900 mb-5">Payment Configuration</h3>
            <div class="space-y-6 max-w-xl">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-4 border-b border-gray-100">
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-1.5">Cash on Delivery (COD)</label>
                        <select name="enable_cod" class="w-full rounded-lg border-gray-300 text-sm focus:border-violet-400 focus:ring-violet-200">
                            <option value="1" {{ ($settings['enable_cod'] ?? '1') == '1' ? 'selected' : '' }}>Enabled</option>
                            <option value="0" {{ ($settings['enable_cod'] ?? '1') == '0' ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-1.5">Online Payments (Razorpay)</label>
                        <select name="enable_razorpay" class="w-full rounded-lg border-gray-300 text-sm focus:border-violet-400 focus:ring-violet-200">
                            <option value="1" {{ ($settings['enable_razorpay'] ?? '1') == '1' ? 'selected' : '' }}>Enabled</option>
                            <option value="0" {{ ($settings['enable_razorpay'] ?? '1') == '0' ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                </div>
                
                <h4 class="text-sm font-bold text-gray-900 mb-2">Razorpay API Setup</h4>
                <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">Razorpay Mode</p>
                        <p class="text-xs text-gray-500 mt-0.5">Toggle between Test and Live environments. Ensure you put in the right keys for the selected mode.</p>
                    </div>
                    <select name="razorpay_mode" class="ml-auto rounded-lg border-gray-300 text-sm focus:border-violet-400 focus:ring-violet-200 font-bold w-32">
                        <option value="test" {{ ($settings['razorpay_mode'] ?? 'test') === 'test' ? 'selected' : '' }}>TEST</option>
                        <option value="live" {{ ($settings['razorpay_mode'] ?? '') === 'live' ? 'selected' : '' }}>LIVE</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <div class="md:col-span-2">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-amber-600 mb-3 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                            Test Mode Keys
                        </h4>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-1.5">Test API Key</label>
                        <input type="text" name="razorpay_test_key" value="{{ $settings['razorpay_test_key'] ?? '' }}" placeholder="rzp_test_..."
                               class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200 font-mono">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-1.5">Test API Secret</label>
                        <input type="password" name="razorpay_test_secret" value="{{ $settings['razorpay_test_secret'] ?? '' }}" placeholder="••••••••"
                               class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                    <div class="md:col-span-2">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-3 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                            Live Mode Keys
                        </h4>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-1.5">Live API Key</label>
                        <input type="text" name="razorpay_live_key" value="{{ $settings['razorpay_live_key'] ?? '' }}" placeholder="rzp_live_..."
                               class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200 font-mono">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-1.5">Live API Secret</label>
                        <input type="password" name="razorpay_live_secret" value="{{ $settings['razorpay_live_secret'] ?? '' }}" placeholder="••••••••"
                               class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200 font-mono">
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <button type="submit" class="px-6 py-2.5 bg-violet-600 text-white rounded-lg font-semibold hover:bg-violet-700 transition-colors text-sm">Save Changes</button>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection
