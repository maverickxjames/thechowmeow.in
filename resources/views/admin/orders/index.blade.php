@extends('layouts.admin')
@section('title', 'Orders')

@section('content')

{{-- Filter Bar --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6">
    <form action="{{ route('admin.orders.index') }}" method="GET">
        <div class="flex flex-wrap gap-3 items-end">

            {{-- Search --}}
            <div class="relative flex-1 min-w-[180px]">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by order # or customer…"
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none">
            </div>

            {{-- Order Status --}}
            <select name="status" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none bg-white">
                <option value="">All Statuses</option>
                @foreach(['pending','paid','shipped','delivered','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>

            {{-- Payment Status --}}
            <select name="payment_status" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none bg-white">
                <option value="">All Payments</option>
                <option value="pending"  {{ request('payment_status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="paid"     {{ request('payment_status') === 'paid'     ? 'selected' : '' }}>Paid</option>
                <option value="failed"   {{ request('payment_status') === 'failed'   ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>

            {{-- Date From --}}
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none">

            {{-- Date To --}}
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none">

            <div class="flex gap-2 ml-auto">
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['search','status','payment_status','date_from','date_to']))
                    <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        Clear
                    </a>
                @endif
                <a href="{{ route('admin.orders.export', request()->query()) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export CSV
                </a>
            </div>
        </div>
    </form>

    {{-- Active filter chips --}}
    @if(request()->hasAny(['search','status','payment_status','date_from','date_to']))
        <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100">
            @if(request('search'))
                <span class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-700 border border-violet-100 px-2.5 py-1 rounded-full">
                    Search: "{{ request('search') }}"
                </span>
            @endif
            @if(request('status'))
                <span class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-700 border border-violet-100 px-2.5 py-1 rounded-full">
                    Status: {{ ucfirst(request('status')) }}
                </span>
            @endif
            @if(request('payment_status'))
                <span class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-700 border border-violet-100 px-2.5 py-1 rounded-full">
                    Payment: {{ ucfirst(request('payment_status')) }}
                </span>
            @endif
            @if(request('date_from'))
                <span class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-700 border border-violet-100 px-2.5 py-1 rounded-full">
                    From: {{ request('date_from') }}
                </span>
            @endif
            @if(request('date_to'))
                <span class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-700 border border-violet-100 px-2.5 py-1 rounded-full">
                    To: {{ request('date_to') }}
                </span>
            @endif
        </div>
    @endif
</div>

{{-- Results count --}}
<div class="flex items-center justify-between mb-3">
    <p class="text-sm text-gray-500">
        Showing <span class="font-semibold text-gray-900">{{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }}</span>
        of <span class="font-semibold text-gray-900">{{ $orders->total() }}</span> orders
    </p>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <th class="px-5 py-3.5">Order</th>
                <th class="px-5 py-3.5">Customer</th>
                <th class="px-5 py-3.5">Total</th>
                <th class="px-5 py-3.5">Payment</th>
                <th class="px-5 py-3.5">Status</th>
                <th class="px-5 py-3.5">Date</th>
                <th class="px-5 py-3.5 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($orders as $order)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5">
                        <span class="font-mono text-xs font-semibold text-gray-900">{{ $order->order_number }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <p class="font-medium text-gray-900">{{ $order->user->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-400">{{ $order->user->email ?? '' }}</p>
                    </td>
                    <td class="px-5 py-3.5 font-semibold text-gray-900">₹{{ number_format($order->total) }}</td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' :
                               ($order->payment_status === 'failed' ? 'bg-red-50 text-red-600' :
                               ($order->payment_status === 'refunded' ? 'bg-blue-50 text-blue-600' :
                               'bg-amber-50 text-amber-700')) }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                            @switch($order->status)
                                @case('pending')  bg-amber-50 text-amber-700 @break
                                @case('paid')     bg-blue-50 text-blue-700 @break
                                @case('shipped')  bg-violet-50 text-violet-700 @break
                                @case('delivered') bg-emerald-50 text-emerald-700 @break
                                @case('cancelled') bg-red-50 text-red-600 @break
                                @default          bg-gray-100 text-gray-500
                            @endswitch">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3.5 text-right">
                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="text-xs font-semibold text-violet-600 hover:text-violet-800 transition-colors">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <p class="text-sm font-medium text-gray-500">No orders found</p>
                        <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $orders->links() }}</div>

@endsection
