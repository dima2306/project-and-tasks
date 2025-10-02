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

        <h2 class="text-xl font-bold mb-6 text-gray-900 dark:text-gray-100">დავალების რედაქტირება</h2>
        <form action="{{ route('admin.tasks.update', $task->id) }}" method="POST">
            @csrf
            {{--
              -- We can use PATCH if we update only one field in a resource
              -- Since we are updating the whole resource, we will use PUT
            --}}
            @method('PUT')

            <div class="mb-4">
                <label for="project_id" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">პროექტი</label>
                <select id="project_id" name="project_id"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100"
                >
                    <option value="">აირჩიეთ პროექტი...</option>
                    @foreach($projects as $project)
                        <option value="{{ old('project_id') ?? $project->id }}"
                            @selected($project->id === $task->project->id)
                        >{{ $project->name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="title" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">სახელი</label>
                <input type="text" name="title" id="title" value="{{ old('title', $task->title) }}"
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">აღწერა</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">{{ old('description', $task->description) }}</textarea>
                @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="status" class="block text-gray-700 dark:text-gray-300 mb-2">სტატუსი</label>
                <select id="status" name="status"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100"
                >
                    <option value="">აირჩიეთ სტატუსი...</option>
                    @foreach($statuses as $value => $status)
                        <option value="{{ old('status') ?? $value }}"
                            @selected($value === $task->status)
                        >{{ $status }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            @can('update', $task)
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded shadow transition hover:cursor-pointer">
                    რედაქტირება
                </button>
            @endcan
            <a href="{{ route('admin.tasks.index') }}" role="button"
               class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded shadow transition">
                უკან სიაში
            </a>
        </form>
    </div>
@endsection
