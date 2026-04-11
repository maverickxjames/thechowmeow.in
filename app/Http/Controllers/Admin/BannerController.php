<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'required|image|max:4096',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:500',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $validated['image_path'] = $request->file('image')->store('banners', 'public');
        $validated['is_active'] = $request->has('is_active');
        unset($validated['image']);

        Banner::create($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:4096',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:500',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($banner->image_path);
            $validated['image_path'] = $request->file('image')->store('banners', 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        unset($validated['image']);

        $banner->update($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated.');
    }

    public function destroy(Banner $banner)
    {
        Storage::disk('public')->delete($banner->image_path);
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted.');
    }
}
