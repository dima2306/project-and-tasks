@extends('admin.layout')
@section('content')
    <div class="m-5 p-4 max-w-4xl mx-auto bg-white dark:bg-gray-800 shadow rounded">
        <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">{{ $project->name }}</h1>
        <p class="mb-4 text-gray-700 dark:text-gray-300">{{ $project->description }}</p>

        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">დამატებითი ინფორმაცია</h2>
            <p class="text-gray-700 dark:text-gray-300">
                <strong>დაწყების
                    თარიღი:</strong> {{ $project->created_at ? $project->created_at->format('Y-m-d') : 'N/A' }}</p>
            <p class="text-gray-700 dark:text-gray-300">
                <strong>სტატუსი:</strong> {{ (int) $project->is_active === 1 ? 'აქტიური' : 'არააქტიური' }}
            </p>
        </div>

        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">მიბმული დავალებები</h2>
            <table class="min-w-full bg-white dark:bg-gray-900 border rounded">
                <thead>
                <tr>
                    <th class="px-4 py-2 border-b text-left">#</th>
                    <th class="px-4 py-2 border-b text-left">სახელი</th>
                    <th class="px-4 py-2 border-b text-left">აღწერა</th>
                    <th class="px-4 py-2 border-b text-left">სტატუსი</th>
                    <th class="px-4 py-2 border-b text-left">დაწყების თარიღი</th>
                    <th class="px-4 py-2 border-b text-left">დასრულების თარიღი</th>
                </tr>
                </thead>
                <tbody>
                @forelse($project->tasks as $task)
                    <tr>
                        <td class="px-4 py-2 border-b">{{ $task->id }}</td>
                        <td class="px-4 py-2 border-b">{{ $task->title }}</td>
                        <td class="px-4 py-2 border-b">{{ $task->description }}</td>
                        <td class="px-4 py-2 border-b">{{ ucfirst($task->status) }}</td>
                        <td class="px-4 py-2 border-b">{{ $task->created_at ? $task->created_at->format('Y-m-d') : 'N/A' }}</td>
                        <td class="px-4 py-2 border-b">{{ $task->completed_at ? $task->completed_at->format('Y-m-d') : 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-center text-gray-500">დავალებები არ არის</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex space-x-4">
            <a href="{{ route('admin.projects.edit', $project->id) }}"
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded shadow transition">
                რედაქტირება
            </a>
            <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST"
                  onsubmit="return confirm('დარწმუნებული ხართ რომ გსურთ წაშლა?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded shadow transition">
                    წაშლა
                </button>
            </form>
            <a href="{{ route('admin.projects.index') }}"
               class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded shadow transition">
                უკან სიაში
            </a>
        </div>
    </div>
@endsection
