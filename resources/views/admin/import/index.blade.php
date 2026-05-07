@extends('layouts.admin')
@section('title', 'Import Products')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Import Products from Excel</h1>
            <p class="text-sm text-gray-500 mt-1">Upload your Excel file (.xlsx, .xls, .csv) to bulk-import products with variants.</p>
        </div>
        <a href="{{ route('admin.import.template') }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Download Template
        </a>
    </div>

    {{-- Success Result --}}
    @if(session('result'))
        @php $result = session('result'); @endphp
        <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-emerald-800">Import Complete!</h3>
                    <p class="text-sm text-emerald-700">Successfully processed your Excel file.</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4 mt-3">
                <div class="bg-white rounded-lg p-3 text-center border border-emerald-100">
                    <p class="text-2xl font-bold text-emerald-700">{{ $result['imported'] }}</p>
                    <p class="text-xs text-emerald-600 mt-0.5">Products Imported</p>
                </div>
                <div class="bg-white rounded-lg p-3 text-center border border-emerald-100">
                    <p class="text-2xl font-bold text-violet-700">{{ $result['variants'] }}</p>
                    <p class="text-xs text-violet-600 mt-0.5">Variants Created</p>
                </div>
                <div class="bg-white rounded-lg p-3 text-center border border-emerald-100">
                    <p class="text-2xl font-bold text-blue-700">{{ $result['categories'] }}</p>
                    <p class="text-xs text-blue-600 mt-0.5">Categories Created</p>
                </div>
            </div>
            @if(!empty($result['errors']))
                <div class="mt-4 p-3 bg-red-50 border border-red-100 rounded-lg">
                    <p class="text-sm font-semibold text-red-700 mb-1">{{ count($result['errors']) }} row(s) had errors:</p>
                    <ul class="text-xs text-red-600 list-disc list-inside space-y-0.5">
                        @foreach(array_slice($result['errors'], 0, 10) as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                        @if(count($result['errors']) > 10)
                            <li>… and {{ count($result['errors']) - 10 }} more</li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>
    @endif

    {{-- Error Flash --}}
    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700 font-medium">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
            <ul class="text-sm text-red-700 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Upload Form --}}
    @if(!isset($preview))
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <form action="{{ route('admin.import.preview') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-8"
                     x-data="{ 
                        fileName: '', 
                        dragging: false,
                        handleFile(e) {
                            const file = e.target.files[0];
                            if (file) this.fileName = file.name;
                        }
                     }"
                     @dragover.prevent="dragging = true"
                     @dragleave.prevent="dragging = false"
                     @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; handleFile({target: $refs.fileInput})">

                    <div class="flex flex-col items-center justify-center border-2 border-dashed rounded-xl py-12 px-6 transition-colors cursor-pointer"
                         :class="dragging ? 'border-violet-400 bg-violet-50' : 'border-gray-200 hover:border-gray-300'"
                         @click="$refs.fileInput.click()">

                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-100 to-violet-100 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>

                        <template x-if="!fileName">
                            <div class="text-center">
                                <p class="text-sm font-semibold text-gray-700">Drop your Excel file here, or <span class="text-violet-600">browse</span></p>
                                <p class="text-xs text-gray-400 mt-1">Supports .xlsx, .xls, .csv — Max 10MB</p>
                            </div>
                        </template>

                        <template x-if="fileName">
                            <div class="text-center">
                                <div class="inline-flex items-center gap-2 px-4 py-2 bg-violet-50 text-violet-700 rounded-lg border border-violet-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="text-sm font-medium" x-text="fileName"></span>
                                </div>
                            </div>
                        </template>

                        <input type="file" name="file" accept=".xlsx,.xls,.csv" class="hidden" x-ref="fileInput" @change="handleFile($event)">
                    </div>
                </div>

                <div class="px-8 pb-6">
                    <button type="submit"
                            class="w-full bg-violet-700 text-white font-semibold py-3 rounded-lg hover:bg-violet-800 transition-colors text-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Preview Import
                    </button>
                </div>
            </form>
        </div>

        {{-- How it works --}}
        <div class="mt-6 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 text-sm mb-4">How Excel Import Works</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex gap-3">
                    <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center shrink-0">
                        <span class="text-sm font-bold text-violet-700">1</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Upload</p>
                        <p class="text-xs text-gray-500 mt-0.5">Upload your Excel file with product data using the format shown in the template.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center shrink-0">
                        <span class="text-sm font-bold text-violet-700">2</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Preview</p>
                        <p class="text-xs text-gray-500 mt-0.5">Review the parsed data before importing. Set the base price for all products.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center shrink-0">
                        <span class="text-sm font-bold text-violet-700">3</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Import</p>
                        <p class="text-xs text-gray-500 mt-0.5">Products, variants, and categories are automatically created. Duplicates are skipped.</p>
                    </div>
                </div>
            </div>

            <div class="mt-5 p-4 bg-gray-50 rounded-lg border border-gray-100">
                <p class="text-xs font-semibold text-gray-600 mb-2">Expected Excel Columns:</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach(['S.No.', 'Product name', 'Category', 'Sub Category', 'Sub Category_2', 'Color', 'Size', 'Quantity', 'Gender'] as $col)
                        <span class="text-xs bg-white text-gray-700 px-2 py-1 rounded border border-gray-200 font-mono">{{ $col }}</span>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    <strong>Sizes & Quantities:</strong> Use <code class="bg-gray-200 px-1 rounded">|</code> to separate multiple values. 
                    E.g. Size: <code class="bg-gray-200 px-1 rounded">XXS | XS | S</code> — Qty: <code class="bg-gray-200 px-1 rounded">2 | 3 | 1</code>
                </p>
            </div>
        </div>
    @endif

    {{-- Preview Table --}}
    @if(isset($preview) && count($preview) > 0)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-gray-900">Preview: {{ $filename }}</h2>
                    <p class="text-xs text-gray-500 mt-0.5">{{ count($preview) }} products found · {{ $skipped }} empty rows skipped</p>
                </div>
                <a href="{{ route('admin.import.index') }}"
                   class="text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">← Upload different file</a>
            </div>

            {{-- Scrollable preview table --}}
            <div class="overflow-x-auto max-h-[420px] overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Product Name</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Sub Category</th>
                            <th class="px-4 py-3">Color</th>
                            <th class="px-4 py-3">Sizes</th>
                            <th class="px-4 py-3">Quantities</th>
                            <th class="px-4 py-3">Gender</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($preview as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-2.5 text-gray-400 font-mono text-xs">{{ $item['row_number'] }}</td>
                                <td class="px-4 py-2.5 font-medium text-gray-900 whitespace-nowrap">{{ Str::limit($item['name'], 35) }}</td>
                                <td class="px-4 py-2.5">
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs rounded-md border border-blue-100">{{ $item['category'] }}</span>
                                </td>
                                <td class="px-4 py-2.5 text-gray-600 text-xs">
                                    {{ $item['sub_category'] }}
                                    @if($item['sub_category_2'])
                                        <span class="text-gray-300">→</span> {{ $item['sub_category_2'] }}
                                    @endif
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-700">
                                        <span class="w-3 h-3 rounded-full border border-gray-200" style="background-color: {{ $item['color'] }}"></span>
                                        {{ $item['color'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($item['sizes'] as $size)
                                            @if(trim($size))
                                                <span class="px-1.5 py-0.5 bg-gray-100 text-gray-600 text-[10px] rounded font-mono">{{ trim($size) }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-2.5">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($item['quantities'] as $qty)
                                            <span class="px-1.5 py-0.5 bg-amber-50 text-amber-700 text-[10px] rounded font-mono border border-amber-100">{{ trim($qty) }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="text-xs {{ $item['gender'] === 'Male' ? 'text-blue-600' : 'text-pink-600' }}">
                                        {{ $item['gender'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Import Action --}}
            <div class="px-6 py-5 bg-gray-50 border-t border-gray-100">
                <form action="{{ route('admin.import.execute') }}" method="POST">
                    @csrf
                    <input type="hidden" name="temp_path" value="{{ $tempPath }}">

                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5 block">Base Price (₹) for all products</label>
                            <input type="number" name="base_price" value="499" min="0" step="1"
                                   class="w-full rounded-lg border-gray-200 text-sm focus:border-violet-400 focus:ring-1 focus:ring-violet-100 py-2.5">
                        </div>
                        <button type="submit"
                                class="px-8 py-2.5 bg-emerald-600 text-white font-semibold text-sm rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2"
                                onclick="return confirm('Import {{ count($preview) }} products with their variants? This action cannot be undone.')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Import {{ count($preview) }} Products
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection
