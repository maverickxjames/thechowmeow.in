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
        <button @click="tab = 'storage'" 
                :class="tab === 'storage' ? 'border-violet-600 text-violet-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="px-4 py-3 font-semibold text-sm border-b-2 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
            Storage
        </button>
        <button @click="tab = 'shipping'" 
                :class="tab === 'shipping' ? 'border-violet-600 text-violet-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="px-4 py-3 font-semibold text-sm border-b-2 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Shipping
        </button>
    </div>

    {{-- Forms --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        
        {{-- General Tab --}}
        <form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data" x-show="tab === 'general'" x-cloak>
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
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Application Logo (Optional)</label>
                    @if(!empty($settings['app_logo']))
                        <div class="mb-3">
                            <img src="{{ Storage::url($settings['app_logo']) }}" alt="App Logo" class="h-12 object-contain bg-gray-50 rounded-md border border-gray-100 p-1">
                        </div>
                    @endif
                    <input type="file" name="app_logo" accept="image/*"
                           class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200 file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 cursor-pointer border bg-white">
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
                <div class="pt-4 mt-4 border-t border-gray-100">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'bg-red-100' : 'bg-gray-100' }}">
                            <svg class="w-4 h-4 {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'text-red-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Maintenance Mode</h4>
                            <p class="text-xs text-gray-500">When enabled, visitors see a maintenance page. Admins can still browse.</p>
                        </div>
                        @if(($settings['maintenance_mode'] ?? '0') === '1')
                            <span class="ml-auto px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full uppercase">&#11044; Active</span>
                        @endif
                    </div>
                    <div class="space-y-3 pl-11">
                        <div>
                            <label class="text-xs font-medium text-gray-600 block mb-1">Status</label>
                            <select name="maintenance_mode" class="w-full rounded-lg text-sm focus:border-violet-400 focus:ring-violet-200 {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'border-red-300 bg-red-50' : 'border-gray-300' }}">
                                <option value="0" {{ ($settings['maintenance_mode'] ?? '0') === '0' ? 'selected' : '' }}>&#128994; Online &mdash; site is fully accessible</option>
                                <option value="1" {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'selected' : '' }}>&#128308; Maintenance &mdash; show maintenance page to visitors</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 block mb-1">Visitor Message</label>
                            <textarea name="maintenance_message" rows="2" placeholder="We're currently performing scheduled maintenance. We'll be back shortly!" class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">{{ $settings['maintenance_message'] ?? '' }}</textarea>
                            <p class="text-xs text-gray-400 mt-1">This message appears on the maintenance page shown to visitors.</p>
                        </div>
                    </div>
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

        {{-- Storage Tab --}}
        <form action="{{ route('admin.settings.store') }}" method="POST" x-show="tab === 'storage'" x-cloak>
            @csrf
            <input type="hidden" name="tab" value="storage">
            <h3 class="text-lg font-bold text-gray-900 mb-1">Product Image Storage</h3>
            <p class="text-sm text-gray-500 mb-6">Choose where new product images will be uploaded. This does not affect existing images already stored.</p>

            @php $currentDisk = $settings['product_image_disk'] ?? 'r2'; @endphp

            {{-- Current Status Banner --}}
            <div class="flex items-center gap-3 mb-6 p-4 rounded-xl border {{ $currentDisk === 'r2' ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200' }}">
                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $currentDisk === 'r2' ? 'bg-blue-100' : 'bg-gray-200' }}">
                    @if($currentDisk === 'r2')
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                    @else
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    @endif
                </div>
                <div>
                    <p class="text-sm font-semibold {{ $currentDisk === 'r2' ? 'text-blue-800' : 'text-gray-700' }}">
                        Currently using: <span class="font-bold">{{ $currentDisk === 'r2' ? 'Cloudflare R2' : 'Local Public Storage' }}</span>
                    </p>
                    <p class="text-xs {{ $currentDisk === 'r2' ? 'text-blue-600' : 'text-gray-500' }} mt-0.5">
                        {{ $currentDisk === 'r2' ? 'Images are stored in your Cloudflare R2 bucket.' : 'Images are stored on the local server disk.' }}
                    </p>
                </div>
                <span class="ml-auto px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $currentDisk === 'r2' ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-600' }}">Active</span>
            </div>

            {{-- Storage Options --}}
            <div class="space-y-3 max-w-xl mb-6">

                {{-- R2 Option --}}
                <label class="flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all {{ $currentDisk === 'r2' ? 'border-violet-400 bg-violet-50' : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50' }}">
                    <input type="radio" name="product_image_disk" value="r2" {{ $currentDisk === 'r2' ? 'checked' : '' }}
                           class="mt-0.5 text-violet-600 focus:ring-violet-400">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                            <p class="font-semibold text-sm text-gray-900">Cloudflare R2</p>
                            <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded uppercase">Cloud</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Scalable object storage with a global CDN. Best for production. Requires R2 credentials in <code class="bg-gray-100 px-1 rounded">.env</code>.</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="text-[11px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full border border-emerald-200">✓ Global CDN</span>
                            <span class="text-[11px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full border border-emerald-200">✓ Scalable</span>
                            <span class="text-[11px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full border border-emerald-200">✓ Free egress</span>
                        </div>
                    </div>
                </label>

                {{-- Public (Local) Option --}}
                <label class="flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all {{ $currentDisk === 'public' ? 'border-violet-400 bg-violet-50' : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50' }}">
                    <input type="radio" name="product_image_disk" value="public" {{ $currentDisk === 'public' ? 'checked' : '' }}
                           class="mt-0.5 text-violet-600 focus:ring-violet-400">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            <p class="font-semibold text-sm text-gray-900">Local Public Storage</p>
                            <span class="px-1.5 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-bold rounded uppercase">Local</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Files saved to <code class="bg-gray-100 px-1 rounded">storage/app/public/</code> on this server. Good for development or small deployments.</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="text-[11px] bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full border border-amber-200">⚠ Server dependent</span>
                            <span class="text-[11px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full border border-emerald-200">✓ No credentials needed</span>
                        </div>
                    </div>
                </label>
            </div>

            {{-- Warning --}}
            <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl mb-6 max-w-xl">
                <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <p class="text-sm font-semibold text-amber-800">Existing images are not migrated</p>
                    <p class="text-xs text-amber-700 mt-0.5">Switching storage only affects <strong>new uploads</strong>. Images already on a previous disk will remain there and their URLs will stay as-is in the database.</p>
                </div>
            </div>

            <button type="submit" class="px-6 py-2.5 bg-violet-600 text-white rounded-lg font-semibold hover:bg-violet-700 transition-colors text-sm">Save Storage Setting</button>
        </form>

        {{-- Shipping Tab --}}
        <form action="{{ route('admin.settings.store') }}" method="POST" x-show="tab === 'shipping'" x-cloak
              x-data='{ 
                domesticSlabs: {{ \App\Models\Setting::where("key", "shipping_config")->value("value") ? (json_encode(json_decode(\App\Models\Setting::where("key", "shipping_config")->value("value"), true)["domestic"]["slabs"] ?? [])) : "[]" }},
                internationalSlabs: {{ \App\Models\Setting::where("key", "shipping_config")->value("value") ? (json_encode(json_decode(\App\Models\Setting::where("key", "shipping_config")->value("value"), true)["international"]["slabs"] ?? [])) : "[]" }},
                addDomestic() { this.domesticSlabs.push({min_weight: 0, max_weight: 0, fee: 0}) },
                removeDomestic(index) { this.domesticSlabs.splice(index, 1) },
                addInternational() { this.internationalSlabs.push({min_weight: 0, max_weight: 0, fee: 0}) },
                removeInternational(index) { this.internationalSlabs.splice(index, 1) }
              }'>
            @csrf
            <input type="hidden" name="tab" value="shipping">
            <h3 class="text-lg font-bold text-gray-900 mb-5">Shipping Configuration</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="p-4 bg-violet-50 rounded-xl border border-violet-100">
                    <label class="text-sm font-bold text-violet-900 block mb-2">Free Shipping Threshold (₹)</label>
                    <div class="relative max-w-xs">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">₹</span>
                        <input type="number" name="free_shipping_threshold" value="{{ $settings['free_shipping_threshold'] ?? '0' }}" 
                               class="w-full pl-7 rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200" placeholder="e.g. 999">
                    </div>
                    <p class="text-[10px] text-violet-600 mt-2">Orders above this amount get free shipping. Set to 0 to disable.</p>
                </div>

                <div class="p-4 bg-violet-50 rounded-xl border border-violet-100">
                    <label class="text-sm font-bold text-violet-900 block mb-2">Calculation Method</label>
                    <select name="shipping_calc_method" class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                        <option value="flat" {{ ($settings['shipping_calc_method'] ?? 'flat') === 'flat' ? 'selected' : '' }}>Flat Rate per Slab (Fixed)</option>
                        <option value="per_kg" {{ ($settings['shipping_calc_method'] ?? 'flat') === 'per_kg' ? 'selected' : '' }}>Price per KG (Fee × Weight)</option>
                    </select>
                    <p class="text-[10px] text-violet-600 mt-2"><b>Flat:</b> Charges the exact fee of the matching slab.<br><b>Per KG:</b> Multiplies the slab fee by total weight.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                {{-- Domestic Shipping --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-gray-900 flex items-center gap-2">
                            🇮🇳 Domestic (INR)
                        </h4>
                        <button type="button" @click="addDomestic()" class="text-xs font-bold text-violet-600 hover:text-violet-700 bg-violet-50 px-2 py-1 rounded">+ Add Slab</button>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-4">
                        <div>
                            <label class="text-xs font-bold text-gray-600 block mb-1">Fixed Fallback Fee (₹)</label>
                            <input type="number" name="shipping_config[domestic][fixed_fee]" value="{{ json_decode($settings['shipping_config'] ?? '{}', true)['domestic']['fixed_fee'] ?? '99' }}" 
                                   class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                            <p class="text-[10px] text-gray-400 mt-1">Used if no product in cart has weight defined.</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-600 block">Weight Slabs (kg)</label>
                            <template x-for="(slab, index) in domesticSlabs" :key="index">
                                <div class="flex items-center gap-2">
                                    <input type="number" step="0.01" :name="`shipping_config[domestic][slabs][${index}][min_weight]`" x-model="slab.min_weight" placeholder="Min" 
                                           class="w-full rounded-lg border-gray-200 text-xs focus:border-violet-400 focus:ring-violet-200">
                                    <span class="text-gray-400 text-xs">-</span>
                                    <input type="number" step="0.01" :name="`shipping_config[domestic][slabs][${index}][max_weight]`" x-model="slab.max_weight" placeholder="Max" 
                                           class="w-full rounded-lg border-gray-200 text-xs focus:border-violet-400 focus:ring-violet-200">
                                    <span class="text-gray-400 text-xs">→</span>
                                    <div class="relative">
                                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]">₹</span>
                                        <input type="number" :name="`shipping_config[domestic][slabs][${index}][fee]`" x-model="slab.fee" placeholder="Fee" 
                                               class="w-24 pl-5 rounded-lg border-gray-200 text-xs focus:border-violet-400 focus:ring-violet-200">
                                    </div>
                                    <button type="button" @click="removeDomestic(index)" class="text-red-500 hover:text-red-600 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- International Shipping --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-gray-900 flex items-center gap-2">
                            🌎 International (USD View)
                        </h4>
                        <button type="button" @click="addInternational()" class="text-xs font-bold text-violet-600 hover:text-violet-700 bg-violet-50 px-2 py-1 rounded">+ Add Slab</button>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-4">
                        <div>
                            <label class="text-xs font-bold text-gray-600 block mb-1">Fixed Fallback Fee (₹)</label>
                            <input type="number" name="shipping_config[international][fixed_fee]" value="{{ json_decode($settings['shipping_config'] ?? '{}', true)['international']['fixed_fee'] ?? '500' }}" 
                                   class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-violet-200">
                            <p class="text-[10px] text-gray-400 mt-1">Fees are stored in INR and converted to USD at checkout.</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-600 block">Weight Slabs (kg)</label>
                            <template x-for="(slab, index) in internationalSlabs" :key="index">
                                <div class="flex items-center gap-2">
                                    <input type="number" step="0.01" :name="`shipping_config[international][slabs][${index}][min_weight]`" x-model="slab.min_weight" placeholder="Min" 
                                           class="w-full rounded-lg border-gray-200 text-xs focus:border-violet-400 focus:ring-violet-200">
                                    <span class="text-gray-400 text-xs">-</span>
                                    <input type="number" step="0.01" :name="`shipping_config[international][slabs][${index}][max_weight]`" x-model="slab.max_weight" placeholder="Max" 
                                           class="w-full rounded-lg border-gray-200 text-xs focus:border-violet-400 focus:ring-violet-200">
                                    <span class="text-gray-400 text-xs">→</span>
                                    <div class="relative">
                                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]">₹</span>
                                        <input type="number" :name="`shipping_config[international][slabs][${index}][fee]`" x-model="slab.fee" placeholder="Fee" 
                                               class="w-24 pl-5 rounded-lg border-gray-200 text-xs focus:border-violet-400 focus:ring-violet-200">
                                    </div>
                                    <button type="button" @click="removeInternational(index)" class="text-red-500 hover:text-red-600 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="px-6 py-2.5 bg-violet-600 text-white rounded-lg font-semibold hover:bg-violet-700 transition-colors text-sm">Save Shipping Settings</button>
        </form>

    </div>
</div>
@endsection
