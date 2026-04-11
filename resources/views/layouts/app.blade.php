<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PetWear — Premium Pet Clothing')</title>
    <meta name="description" content="@yield('meta_description', 'Shop premium clothing for dogs and cats. Casual wear, festive outfits, accessories and more.')">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-white text-gray-900">

    {{-- Announcement Bar --}}
    <div class="bg-gray-950 text-gray-300 text-center py-2.5 text-xs tracking-wider font-medium">
        Free shipping on orders above {{ currency_format(999) }} &nbsp;&middot;&nbsp; Use code <span class="text-white font-semibold">PETWEAR10</span> for 10% off
    </div>

    {{-- Navigation --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50" x-data="{ mobileMenu: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center gap-6">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
                    <svg class="w-7 h-7 text-violet-700" viewBox="0 0 100 100" fill="currentColor">
                        <ellipse cx="50" cy="68" rx="22" ry="18"/>
                        <ellipse cx="28" cy="44" rx="10" ry="12" transform="rotate(-15 28 44)"/>
                        <ellipse cx="72" cy="44" rx="10" ry="12" transform="rotate(15 72 44)"/>
                        <ellipse cx="38" cy="28" rx="9" ry="11" transform="rotate(-10 38 28)"/>
                        <ellipse cx="62" cy="28" rx="9" ry="11" transform="rotate(10 62 28)"/>
                    </svg>
                    <span class="text-xl font-bold text-gray-900 tracking-tight">PetWear</span>
                </a>

                {{-- Desktop Menu --}}
                <div class="hidden lg:flex items-center gap-0.5">
                    @if(isset($headerMenu) && $headerMenu)
                        @foreach($headerMenu->items as $item)
                            @if($item->is_active)
                                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                                    <a href="{{ $item->resolved_url }}"
                                       class="px-3.5 py-2 text-sm font-medium text-gray-700 hover:text-violet-700 rounded-md hover:bg-violet-50 flex items-center gap-1 transition-colors whitespace-nowrap">
                                        {{ $item->title }}
                                        @if($item->children->count())
                                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        @endif
                                    </a>
                                    @if($item->children->count())
                                        <div x-show="open"
                                             x-cloak
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 translate-y-1"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 translate-y-0"
                                             x-transition:leave-end="opacity-0 translate-y-1"
                                             class="absolute left-0 top-full mt-1 w-52 bg-white rounded-xl shadow-lg ring-1 ring-gray-900/5 py-1.5 z-[60]">
                                            @foreach($item->children as $child)
                                                @if($child->is_active)
                                                    <a href="{{ $child->resolved_url }}" class="block px-4 py-2 text-sm text-gray-600 hover:bg-violet-50 hover:text-violet-700 transition-colors">{{ $child->title }}</a>
                                                    @foreach($child->children as $subChild)
                                                        @if($subChild->is_active)
                                                            <a href="{{ $subChild->resolved_url }}" class="block pl-7 py-1.5 text-xs text-gray-500 hover:bg-violet-50 hover:text-violet-700 transition-colors">{{ $subChild->title }}</a>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>

                {{-- Right Actions --}}
                <div class="flex items-center gap-1">

                    {{-- Currency Switcher --}}
                    @if(config('petwear.enable_usd', false))
                        <div class="relative mr-1 hidden md:block" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-600 hover:text-violet-700 hover:bg-violet-50 rounded-lg transition-colors">
                                <span class="uppercase">{{ session('currency', 'INR') }}</span>
                                <svg class="w-3 h-3 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute right-0 top-full mt-1 w-28 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5 z-[60]">
                                <a href="{{ route('currency.switch', 'INR') }}" class="flex items-center justify-between px-3 py-1.5 text-xs text-gray-600 hover:bg-violet-50 hover:text-violet-700 transition-colors {{ session('currency', 'INR') === 'INR' ? 'font-bold bg-violet-50 text-violet-700' : '' }}">
                                    INR <span class="text-[10px] text-gray-400 font-normal">₹</span>
                                </a>
                                <a href="{{ route('currency.switch', 'USD') }}" class="flex items-center justify-between px-3 py-1.5 text-xs text-gray-600 hover:bg-violet-50 hover:text-violet-700 transition-colors {{ session('currency', 'INR') === 'USD' ? 'font-bold bg-violet-50 text-violet-700' : '' }}">
                                    USD <span class="text-[10px] text-gray-400 font-normal">$</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Search --}}
                    <form action="{{ route('products.index') }}" method="GET" class="hidden md:block">
                        <div class="relative">
                            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" name="search" placeholder="Search products..."
                                   value="{{ request('search') }}"
                                   class="pl-9 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-violet-400 focus:ring-1 focus:ring-violet-100 transition-all w-44 focus:w-56 outline-none">
                        </div>
                    </form>

                    {{-- Cart --}}
                    <a href="{{ route('cart.index') }}" class="relative p-2.5 text-gray-500 hover:text-violet-700 hover:bg-violet-50 rounded-lg transition-colors" aria-label="Cart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        @if(isset($cart) && $cart && $cart->item_count > 0)
                            <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-violet-700 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none">{{ $cart->item_count }}</span>
                        @endif
                    </a>

                    {{-- Auth --}}
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 p-1 rounded-lg hover:bg-gray-100 transition-colors ml-1">
                                <div class="w-8 h-8 rounded-full bg-violet-700 flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            </button>
                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 top-full mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5 z-50 origin-top-right"
                                 style="display:none">
                                <div class="px-4 py-2.5 border-b border-gray-100 mb-1">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                @if(auth()->user()->is_admin)
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-violet-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                        Admin Panel
                                    </a>
                                @endif
                                <a href="{{ route('orders.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-violet-700 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    My Orders
                                </a>
                                <div class="border-t border-gray-100 mt-1 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            Sign Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-violet-700 transition-colors px-3 py-2">Login</a>
                        <a href="{{ route('register') }}" class="text-sm font-semibold px-4 py-2 bg-violet-700 text-white rounded-lg hover:bg-violet-800 transition-colors ml-1">Sign Up</a>
                    @endauth

                    {{-- Mobile Toggle --}}
                    <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors ml-1" aria-label="Menu">
                        <svg x-show="!mobileMenu" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileMenu" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenu"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="lg:hidden border-t border-gray-100 bg-white">
            <div class="max-w-7xl mx-auto px-4 py-3 space-y-0.5">
                @if(isset($headerMenu) && $headerMenu)
                    @foreach($headerMenu->items as $item)
                        @if($item->is_active)
                            @if($item->children->count())
                                <div x-data="{ expanded: false }">
                                    <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">
                                        <span>{{ $item->title }}</span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    <div x-show="expanded" x-cloak x-transition class="pl-4 space-y-0.5 mt-0.5">
                                        <a href="{{ $item->resolved_url }}" class="block px-3 py-2 rounded-lg text-sm text-violet-700 font-medium hover:bg-violet-50 transition-colors">All {{ $item->title }}</a>
                                        @foreach($item->children as $child)
                                            @if($child->is_active)
                                                <a href="{{ $child->resolved_url }}" class="block px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-violet-50 hover:text-violet-700 transition-colors">{{ $child->title }}</a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="{{ $item->resolved_url }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">{{ $item->title }}</a>
                            @endif
                        @endif
                    @endforeach
                @endif
                <div class="pt-2 pb-1">
                    <form action="{{ route('products.index') }}" method="GET">
                        <div class="relative">
                            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" name="search" placeholder="Search products..."
                                   class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
            <div class="flex items-center justify-between bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 ml-4 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
            <div class="flex items-center justify-between bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-red-400 hover:text-red-600 ml-4 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-950 text-gray-400 mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">

                {{-- Brand --}}
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-2.5 mb-5">
                        <svg class="w-6 h-6 text-violet-400" viewBox="0 0 100 100" fill="currentColor">
                            <ellipse cx="50" cy="68" rx="22" ry="18"/>
                            <ellipse cx="28" cy="44" rx="10" ry="12" transform="rotate(-15 28 44)"/>
                            <ellipse cx="72" cy="44" rx="10" ry="12" transform="rotate(15 72 44)"/>
                            <ellipse cx="38" cy="28" rx="9" ry="11" transform="rotate(-10 38 28)"/>
                            <ellipse cx="62" cy="28" rx="9" ry="11" transform="rotate(10 62 28)"/>
                        </svg>
                        <span class="text-lg font-bold text-white">PetWear</span>
                    </div>
                    <p class="text-sm leading-relaxed text-gray-500">Premium clothing for your beloved pets. Thoughtfully designed for comfort and style.</p>
                </div>

                {{-- Shop --}}
                <div>
                    <h4 class="text-xs font-semibold text-gray-300 uppercase tracking-widest mb-5">Shop</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('products.index') }}" class="text-sm text-gray-500 hover:text-gray-200 transition-colors">All Products</a></li>
                        <li><a href="{{ route('page.show', 'about-us') }}" class="text-sm text-gray-500 hover:text-gray-200 transition-colors">About Us</a></li>
                        <li><a href="{{ route('page.show', 'privacy-policy') }}" class="text-sm text-gray-500 hover:text-gray-200 transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('page.show', 'terms-and-conditions') }}" class="text-sm text-gray-500 hover:text-gray-200 transition-colors">Terms &amp; Conditions</a></li>
                    </ul>
                </div>

                {{-- Categories --}}
                <div>
                    <h4 class="text-xs font-semibold text-gray-300 uppercase tracking-widest mb-5">Categories</h4>
                    <ul class="space-y-3">
                        @if(isset($categoryTree))
                            @foreach($categoryTree->take(6) as $cat)
                                <li><a href="{{ route('category.show', $cat->slug) }}" class="text-sm text-gray-500 hover:text-gray-200 transition-colors">{{ $cat->name }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h4 class="text-xs font-semibold text-gray-300 uppercase tracking-widest mb-5">Contact</h4>
                    <ul class="space-y-3.5">
                        <li class="flex items-center gap-2.5 text-sm text-gray-500">
                            <svg class="w-4 h-4 text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            support@petwear.com
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-gray-500">
                            <svg class="w-4 h-4 text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            +91 98765 43210
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-gray-500">
                            <svg class="w-4 h-4 text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Mumbai, India
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800/70 mt-12 pt-8 flex flex-col sm:flex-row justify-between items-center gap-3">
                <p class="text-xs text-gray-600">&copy; {{ date('Y') }} PetWear. All rights reserved.</p>
                <p class="text-xs text-gray-600">Crafted with care for pet lovers everywhere.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
