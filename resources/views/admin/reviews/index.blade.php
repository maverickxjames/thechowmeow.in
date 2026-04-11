@extends('layouts.admin')
@section('title', 'Reviews')

@section('content')

{{-- Filter Bar --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6">
    <form action="{{ route('admin.reviews.index') }}" method="GET">
        <div class="flex flex-wrap gap-3 items-end">

            {{-- Search --}}
            <div class="relative flex-1 min-w-[180px]">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by customer or product…"
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none">
            </div>

            {{-- Status --}}
            <select name="status" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none bg-white">
                <option value="">All Status</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
            </select>

            {{-- Rating --}}
            <select name="rating" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none bg-white">
                <option value="">All Ratings</option>
                @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                @endfor
            </select>

            <div class="flex gap-2 ml-auto">
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['search','status','rating']))
                    <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        Clear
                    </a>
                @endif
                <a href="{{ route('admin.reviews.export', request()->query()) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export CSV
                </a>
            </div>
        </div>
    </form>

    {{-- Active filter chips --}}
    @if(request()->hasAny(['search','status','rating']))
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
            @if(request('rating'))
                <span class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-700 border border-violet-100 px-2.5 py-1 rounded-full">
                    Rating: {{ request('rating') }} star{{ request('rating') > 1 ? 's' : '' }}
                </span>
            @endif
        </div>
    @endif
</div>

{{-- Results count --}}
<div class="flex items-center justify-between mb-3">
    <p class="text-sm text-gray-500">
        Showing <span class="font-semibold text-gray-900">{{ $reviews->firstItem() ?? 0 }}–{{ $reviews->lastItem() ?? 0 }}</span>
        of <span class="font-semibold text-gray-900">{{ $reviews->total() }}</span> reviews
    </p>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <th class="px-5 py-3.5">Customer</th>
                <th class="px-5 py-3.5">Product</th>
                <th class="px-5 py-3.5">Rating</th>
                <th class="px-5 py-3.5">Comment</th>
                <th class="px-5 py-3.5">Status</th>
                <th class="px-5 py-3.5">Date</th>
                <th class="px-5 py-3.5 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($reviews as $review)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5 font-medium text-gray-900">{{ $review->user->name ?? 'N/A' }}</td>
                    <td class="px-5 py-3.5 text-gray-600">{{ $review->product->name ?? 'N/A' }}</td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                            <span class="ml-1 text-xs text-gray-500">{{ $review->rating }}/5</span>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-gray-600 max-w-xs">
                        <p class="truncate">{{ $review->comment }}</p>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $review->is_approved ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $review->is_approved ? 'Approved' : 'Pending' }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $review->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-3">
                            @if(!$review->is_approved)
                                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button class="text-xs font-semibold text-emerald-600 hover:text-emerald-800 transition-colors">Approve</button>
                                </form>
                            @else
                                <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button class="text-xs font-semibold text-amber-600 hover:text-amber-800 transition-colors">Reject</button>
                                </form>
                            @endif
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                                  onsubmit="return confirm('Delete this review?')">
                                @csrf @method('DELETE')
                                <button class="text-xs font-semibold text-red-500 hover:text-red-700 transition-colors">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <p class="text-sm font-medium text-gray-500">No reviews found</p>
                        <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $reviews->links() }}</div>

@endsection
