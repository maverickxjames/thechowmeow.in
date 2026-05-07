<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin | @yield('title', 'Dashboard') - {{ config('app.name', 'PetWear') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <style>
        .sidebar-section-label { font-size: 0.625rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #6b7280; padding: 0.75rem 1rem 0.35rem; }
        .sidebar-link { display: flex; align-items: center; gap: 0.65rem; padding: 0.5rem 0.75rem; margin: 0 0.5rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 500; color: #9ca3af; transition: all 0.15s; }
        .sidebar-link:hover { background: rgba(139,92,246,0.08); color: #e5e7eb; }
        .sidebar-link.active { background: rgba(139,92,246,0.15); color: #a78bfa; }
        .sidebar-link .sidebar-icon { width: 1.125rem; height: 1.125rem; flex-shrink: 0; opacity: 0.7; }
        .sidebar-link.active .sidebar-icon { opacity: 1; }
        .sidebar-divider { height: 1px; background: #1f2937; margin: 0.5rem 0.75rem; }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50" x-data="{ sidebarOpen: true, mobileSidebar: false }">
    <div class="flex min-h-screen">
        {{-- Sidebar Overlay (mobile) --}}
        <div x-show="mobileSidebar" x-cloak @click="mobileSidebar = false"
             class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-transition.opacity></div>

        {{-- Sidebar --}}
        <aside :class="[sidebarOpen ? 'w-[250px]' : 'w-[68px]', mobileSidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0']"
               class="bg-gray-950 fixed h-full z-50 transition-all duration-300 flex flex-col overflow-y-auto overflow-x-hidden scrollbar-none">

            {{-- Logo --}}
            <div class="flex items-center h-16 px-4 border-b border-white/5 shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 overflow-hidden">
                    @if(config('app.logo'))
                        <div class="w-8 h-8 flex items-center justify-center shrink-0">
                            <img src="{{ Storage::url(config('app.logo')) }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                    @else
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shrink-0">
                            <svg class="w-4.5 h-4.5 text-white" viewBox="0 0 100 100" fill="currentColor">
                                <ellipse cx="50" cy="68" rx="22" ry="18"/>
                                <ellipse cx="28" cy="44" rx="10" ry="12" transform="rotate(-15 28 44)"/>
                                <ellipse cx="72" cy="44" rx="10" ry="12" transform="rotate(15 72 44)"/>
                                <ellipse cx="38" cy="28" rx="9" ry="11" transform="rotate(-10 38 28)"/>
                                <ellipse cx="62" cy="28" rx="9" ry="11" transform="rotate(10 62 28)"/>
                            </svg>
                        </div>
                    @endif
                    <span x-show="sidebarOpen" x-transition class="text-base font-bold text-white whitespace-nowrap">{{ config('app.name', 'PetWear') }}</span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" class="ml-auto p-1.5 rounded-md hover:bg-white/5 text-gray-500 hover:text-gray-300 hidden lg:block transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                        <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 py-3">

                {{-- OVERVIEW --}}
                <p class="sidebar-section-label" x-show="sidebarOpen" x-transition>Overview</p>
                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z"/></svg>
                    <span x-show="sidebarOpen" x-transition>Dashboard</span>
                </a>

                <div class="sidebar-divider"></div>

                {{-- CATALOG --}}
                <p class="sidebar-section-label" x-show="sidebarOpen" x-transition>Catalog</p>
                <a href="{{ route('admin.categories.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    <span x-show="sidebarOpen" x-transition>Categories</span>
                </a>
                <a href="{{ route('admin.products.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span x-show="sidebarOpen" x-transition>Products</span>
                </a>
                <a href="{{ route('admin.inventory.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span x-show="sidebarOpen" x-transition>Inventory</span>
                </a>
                <a href="{{ route('admin.import.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.import.*') ? 'active' : '' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span x-show="sidebarOpen" x-transition>Import</span>
                </a>
                <a href="{{ route('admin.reviews.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    <span x-show="sidebarOpen" x-transition>Reviews</span>
                </a>

                <div class="sidebar-divider"></div>

                {{-- SALES --}}
                <p class="sidebar-section-label" x-show="sidebarOpen" x-transition>Sales</p>
                <a href="{{ route('admin.orders.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span x-show="sidebarOpen" x-transition>Orders</span>
                </a>
                <a href="{{ route('admin.coupons.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span x-show="sidebarOpen" x-transition>Coupons</span>
                </a>

                <div class="sidebar-divider"></div>

                {{-- CONTENT --}}
                <p class="sidebar-section-label" x-show="sidebarOpen" x-transition>Content</p>
                <a href="{{ route('admin.menus.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h7"/></svg>
                    <span x-show="sidebarOpen" x-transition>Menus</span>
                </a>
                <a href="{{ route('admin.banners.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span x-show="sidebarOpen" x-transition>Banners</span>
                </a>
                <a href="{{ route('admin.pages.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-show="sidebarOpen" x-transition>Pages</span>
                </a>

                <div class="sidebar-divider"></div>

                {{-- SYSTEM --}}
                <p class="sidebar-section-label" x-show="sidebarOpen" x-transition>System</p>
                <a href="{{ route('admin.settings.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span x-show="sidebarOpen" x-transition>Settings</span>
                </a>
            </nav>

            {{-- Bottom Section --}}
            <div class="border-t border-white/5 py-3 shrink-0">
                <a href="{{ route('home') }}" target="_blank"
                   class="sidebar-link">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span x-show="sidebarOpen" x-transition>View Store</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-link w-full text-red-400/80 hover:text-red-400 hover:bg-red-500/10">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span x-show="sidebarOpen" x-transition>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <main :class="sidebarOpen ? 'lg:ml-[250px]' : 'lg:ml-[68px]'" class="flex-1 transition-all duration-300 min-h-screen">
            {{-- Top Bar --}}
            <header class="bg-white border-b border-gray-200 px-5 lg:px-8 h-16 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    {{-- Mobile toggle --}}
                    <button @click="mobileSidebar = !mobileSidebar" class="lg:hidden p-2 -ml-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900 leading-tight">@yield('title', 'Dashboard')</h1>
                        @hasSection('subtitle')
                            <p class="text-xs text-gray-400 mt-0.5">@yield('subtitle')</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500 hidden sm:block">{{ auth()->user()->name }}</span>
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
            </header>

            {{-- Flash Messages --}}
            <div class="px-5 lg:px-8 pt-5">
                @if(session('success'))
                    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3 rounded-xl mb-4"
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                        <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-3 rounded-xl mb-4"
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
                        <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                        <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-3 rounded-xl mb-4">
                        <ul class="list-disc list-inside text-sm space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- Page Content --}}
            <div class="px-5 lg:px-8 pb-8">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
