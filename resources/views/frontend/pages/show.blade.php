@extends('layouts.app')
@section('title', $page->meta_title ?: $page->title . ' — ' . config('app.name', 'PetWear'))

@section('content')
<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <nav class="flex items-center gap-2 text-sm text-gray-400 mb-2">
            <a href="{{ route('home') }}" class="hover:text-violet-700 transition-colors">Home</a>
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-700 font-medium">{{ $page->title }}</span>
        </nav>
        <h1 class="text-3xl font-bold text-gray-900">{{ $page->title }}</h1>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8 md:p-12">
        <div class="prose prose-gray prose-lg max-w-none">{!! $page->content !!}</div>
    </div>
</div>
@endsection
