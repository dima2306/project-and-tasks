@extends('admin.layout')
@section('content')
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

    <div class="p-2 max-w-fit">
        <a href="{{ route('admin.projects.create') }}" title="ახალი პროექტი"
           class="inline-flex px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded shadow transition">
            <x-icon-plus class="h-5 w-5 mr-2" />
            ახალი პროექტი
        </a>
    </div>
    <div class="p-2">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                    სახელი
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                    აღწერა
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                    ქმედებები
                </th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($projects as $project)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ $project->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $project->description }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <a href="{{ route('admin.projects.show', $project->id) }}"
                           class="inline-block text-blue-500 hover:text-blue-700 mr-2" title="ნახვა">
                            <x-icon-eye class="size-5" />
                        </a>
                        <a href="{{ route('admin.projects.edit', $project->id) }}"
                           class="inline-block text-blue-500 hover:text-blue-700 mr-2" title="რედაქტირება">
                            <x-icon-pencil class="size-5" />
                        </a>
                        <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST"
                              class="inline-block" onsubmit="return confirm('დარწმუნებული ხართ რომ გსურთ წაშლა?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 hover:cursor-pointer"
                                    title="წაშლა">
                                <x-icon-trash class="size-5" />
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-gray-500 dark:text-gray-300">
                        პროექტები არ არის
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
