@extends('layouts.admin')
@section('title', 'Add Page')

@push('styles')
<style>
    .ck-editor__editable { min-height: 320px; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#content'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'underline', 'strikethrough', '|',
                      'bulletedList', 'numberedList', 'blockQuote', '|',
                      'link', 'insertTable', 'mediaEmbed', '|',
                      'outdent', 'indent', '|', 'undo', 'redo'],
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                ]
            }
        })
        .then(editor => {
            // Sync editor content back to textarea before form submit
            document.querySelector('form').addEventListener('submit', () => {
                document.querySelector('#content').value = editor.getData();
            });
        })
        .catch(console.error);
</script>
@endpush

@section('content')
<div class="max-w-3xl">
    <form action="{{ route('admin.pages.store') }}" method="POST"
          class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 space-y-6">
        @csrf

        <div>
            <label class="text-sm font-semibold text-gray-700 block mb-1">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none">
        </div>

        <div>
            <label class="text-sm font-semibold text-gray-700 block mb-1">Slug</label>
            <input type="text" name="slug" value="{{ old('slug') }}" placeholder="auto-generated from title"
                   class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none">
        </div>

        <div>
            <label class="text-sm font-semibold text-gray-700 block mb-1">Content</label>
            <textarea id="content" name="content">{{ old('content') }}</textarea>
        </div>

        <div>
            <label class="text-sm font-semibold text-gray-700 block mb-1">Meta Title</label>
            <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                   class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none">
        </div>

        <div>
            <label class="text-sm font-semibold text-gray-700 block mb-1">Meta Description</label>
            <textarea name="meta_description" rows="2"
                      class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-violet-400 focus:ring-1 focus:ring-violet-100 outline-none resize-none">{{ old('meta_description') }}</textarea>
        </div>

        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" checked class="rounded text-violet-600">
            <span class="text-sm text-gray-700">Active (visible on site)</span>
        </label>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="px-6 py-2.5 bg-violet-600 text-white text-sm font-semibold rounded-lg hover:bg-violet-700 transition-colors">
                Create Page
            </button>
            <a href="{{ route('admin.pages.index') }}"
               class="px-6 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
