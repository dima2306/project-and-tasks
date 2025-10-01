@extends('admin.layout')
@section('content')
    <div class="max-w-lg mx-auto mt-8 bg-white dark:bg-gray-900 p-6 rounded shadow">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show"
                 class="m-2 flex items-center justify-between bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                 role="alert">
                <span>{{ session('success') }}</span>
                <button type="button" @click="show = false"
                        class="ml-4 text-green-700 hover:text-green-900 hover:cursor-pointer font-bold">
                    &times;
                </button>
            </div>
        @endif

        <h2 class="text-xl font-bold mb-6 text-gray-900 dark:text-gray-100">ახალი პროექტი</h2>
        <form action="{{ route('admin.projects.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">სახელი</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">აღწერა</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">{{ old('description') }}</textarea>
                @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6 flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                class="mr-2">
                <label for="is_active" class="text-gray-700 dark:text-gray-300">აქტიური</label>
            </div>

            @can('create', \App\Models\Project::class)
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded shadow transition hover:cursor-pointer">
                შექმნა
            </button>
            @endcan
            <a href="{{ route('admin.projects.index') }}" role="button"
               class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded shadow transition">
                უკან სიაში
            </a>
        </form>
    </div>
@endsection
