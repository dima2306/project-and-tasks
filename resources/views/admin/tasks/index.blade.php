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

    @can('create', \App\Models\Task::class)
        <div class="p-2 max-w-fit">
            <a href="{{ route('admin.tasks.create') }}" title="ახალი დავალება"
               class="inline-flex px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded shadow transition">
                <x-icon-plus class="h-5 w-5 mr-2" />
                ახალი დავალება
            </a>
        </div>
    @endcan
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                    დასრულების თარიღი
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                    ქმედებები
                </th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($tasks as $task)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ $task->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $task->description }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $task->completed_at }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <a href="{{ route('admin.projects.show', $task->id) }}"
                           class="inline-block text-blue-500 hover:text-blue-700 mr-2" title="ნახვა">
                            <x-icon-eye class="size-5" />
                        </a>
                        @can('update', $task)
                            <a href="{{ route('admin.projects.edit', $task->id) }}"
                               class="inline-block text-blue-500 hover:text-blue-700 mr-2" title="რედაქტირება">
                                <x-icon-pencil class="size-5" />
                            </a>
                        @endcan
                        @can('delete', $task)
                            <form action="{{ route('admin.projects.destroy', $task->id) }}" method="POST"
                                  class="inline-block" onsubmit="return confirm('დარწმუნებული ხართ რომ გსურთ წაშლა?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 hover:cursor-pointer"
                                        title="წაშლა">
                                    <x-icon-trash class="size-5" />
                                </button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500 dark:text-gray-300">
                        დავალებები არ არის
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
