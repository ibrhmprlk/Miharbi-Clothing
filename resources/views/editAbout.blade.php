<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<title>Edit About</title>
</head>

<body class="bg-gray-100 min-h-screen flex items-start justify-center py-10">

<!-- Notification -->
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     class="fixed top-5 right-5 z-50 bg-green-500 text-white px-5 py-3 rounded-lg shadow-lg">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     class="fixed top-5 right-5 z-50 bg-red-500 text-white px-5 py-3 rounded-lg shadow-lg">
    {{ session('error') }}
</div>
@endif

<div class="w-full max-w-4xl bg-white rounded-2xl shadow-lg p-8">

    <div class="flex items-center justify-between mb-6 border-b pb-4">
        <h2 class="text-2xl font-bold text-gray-800">
            Edit About
        </h2>
        <a href="{{ url('about') }}" class="text-sm font-medium text-green-600 hover:text-green-700 transition flex items-center">
            ← Back to About Page
        </a>
    </div>

    <form method="POST" action="{{ route('updateAbout', $ourPost->id) }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title</label>
                <input type="text" name="title" value="{{ old('title', $ourPost->title) }}"
                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Image URL</label>
                <input type="url" name="image" value="{{ old('image', $ourPost->image) }}"
                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Phone</label>
              <input
    type="tel"
    name="phone"
    value="{{ old('phone', $ourPost->phone) }}"
    maxlength="18"
    placeholder="+90 5xx xxx xx xx"
    oninput="this.value = this.value.replace(/[^0-9+ ]/g, '')"
    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">

            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $ourPost->email) }}"
                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Main Description</label>
            <textarea name="description" rows="3"
                class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">{{ old('description', $ourPost->description) }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Second Paragraph</label>
                <textarea name="second_paragraph" rows="4"
                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">{{ old('second_paragraph', $ourPost->second_paragraph) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Last Paragraph</label>
                <textarea name="last_paragraph" rows="4"
                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">{{ old('last_paragraph', $ourPost->last_paragraph) }}</textarea>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-xl">
            <h3 class="text-sm font-bold text-gray-600 mb-3 uppercase tracking-wider">Social Profiles</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input type="url" name="instagram_url" placeholder="Instagram URL"
                    value="{{ old('instagram_url', $ourPost->instagram_url) }}"
                    class="rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">

                <input type="url" name="twitter_url" placeholder="Twitter URL"
                    value="{{ old('twitter_url', $ourPost->twitter_url) }}"
                    class="rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">

                <input type="url" name="github_url" placeholder="GitHub URL"
                    value="{{ old('github_url', $ourPost->github_url) }}"
                    class="rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">

                <input type="url" name="linkedin_url" placeholder="LinkedIn URL"
                    value="{{ old('linkedin_url', $ourPost->linkedin_url) }}"
                    class="rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white px-8 py-2.5 rounded-lg font-bold shadow-md hover:shadow-lg transition-all active:scale-95">
                Save Changes
            </button>
        </div>

    </form>
</div>

</body>
</html>