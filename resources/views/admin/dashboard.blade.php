@extends('admin.layout')
@section('content')
    <main class="flex-1 p-6 overflow-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">პროექტები</h2>
                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">0</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">დავალებები</h2>
                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">0</p>
            </div>
        </div>
    </main>
@endsection
