@extends('admin.layout')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-800">დავალების დეტალები</h1>
                    <div class="flex space-x-2">
                        @can('update', $task)
                            <a href="{{ route('admin.tasks.edit', $task->id) }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                <x-icon-pencil class="size-4 mr-2" />
                                რედაქტირება
                            </a>
                        @endcan
                        @can('delete', $task)
                            <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST"
                                  class="inline-block"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('დარწმუნებული ხართ რომ გსურთ წაშლა?')"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                    <x-icon-trash class="size-4 mr-2" />
                                    წაშლა
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">დავალების ინფორმაცია</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">დასახელება</label>
                                <p class="mt-1 text-lg text-gray-900">{{ $task->title }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600">აღწერა</label>
                                <p class="mt-1 text-gray-800 whitespace-pre-line">{{ $task->description }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600">სტატუსი</label>
                                <span class="mt-1 inline-block px-3 py-1 rounded-full text-sm font-medium
                                    @if($task->status === 'todo')
                                        bg-yellow-100 text-yellow-800
                                    @elseif($task->status === 'in_progress')
                                        bg-blue-100 text-blue-800
                                    @elseif($task->status === 'completed')
                                        bg-green-100 text-green-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif">
                                    {{-- Here we are using table-lookup for fast array scan and  better readability --}}
                                    {{ $taskStatuses[$task->status->value] ?? 'უცნობი' }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600">შექმნის თარიღი</label>
                                <p class="mt-1 text-gray-800">{{ $task->created_at->format('d.m.Y H:i') }}</p>
                            </div>

                            @if($task->updated_at != $task->created_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">ბოლო განახლება</label>
                                    <p class="mt-1 text-gray-800">{{ $task->updated_at->format('d.m.Y H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">დაკავშირებული პროექტი</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">პროექტის სახელი</label>
                                    @can('view', $task->project)
                                        <a href="{{ route('admin.projects.show', $task->project->id) }}"
                                           class="mt-1 text-lg text-blue-600 hover:text-blue-800 hover:underline">
                                            {{ $task->project->name }}
                                        </a>
                                    @else
                                        <p class="mt-1 text-lg text-gray-900">{{ $task->project->name }}</p>
                                    @endcan
                                </div>

                                @if($task->project->description)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">პროექტის აღწერა</label>
                                        <p class="mt-1 text-gray-700 text-sm">{{ Str::limit($task->project->description, 150) }}</p>
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium text-gray-600">პროექტის მფლობელი</label>
                                    <p class="mt-1 text-gray-800">{{ $task->project->user->name }}</p>
                                </div>

                                <div class="pt-2">
                                    @can('view', $task->project)
                                        <a href="{{ route('admin.projects.show', $task->project->id) }}"
                                           class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                            პროექტის ნახვა
                                            <x-icon-chevron-right class="size-4" />
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between bg-gray-50 px-6 py-4 border-t ">
                <a href="{{ route('admin.tasks.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    <x-icon-arrow-left class="size-4 mr-1" />
                    უკან დაბრუნება
                </a>
                @if($task->status !== 'completed')
                    @can('update', $task)
                        <button title="აღნიშნული დავალების დასრულებულად მონიშვნა" role="link"
                                id="complete-task-button" data-task-id="{{ $task->id }}"
                                class="inline-flex px-4 py-2 text-white rounded-md bg-green-600 hover:bg-green-700 hover:cursor-pointer">
                            <x-icon-check class="size-5 mr-2" />
                            <span id="complete-button-text">დასრულება</span>
                        </button>
                    @endcan
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const button = document.querySelector('#complete-task-button');

            if (button) {
                button.addEventListener('click', function() {
                    const button = this;
                    const buttonText = document.querySelector('#complete-button-text');
                    const taskId = this.dataset.taskId;

                    button.disabled = true;
                    buttonText.textContent = 'მუშავდება...';

                    fetch(`/api/tasks/${taskId}/completed`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                const statusSpan = document.querySelector('[class*="bg-yellow-100"], [class*="bg-blue-100"], [class*="bg-green-100"]');

                                if (statusSpan) {
                                    statusSpan.textContent = 'დასრულებული';
                                    statusSpan.className = 'mt-1 inline-block px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
                                }

                                button.outerHTML = `
                                    <span class="inline-flex px-4 py-2 text-green-700 bg-green-100 rounded-md">
                                        <x-icon-check class="size-5 mr-2" />
                                        დასრულებული
                                    </span>
                                `;
                            } else {
                                alert(data.message || 'დაფიქსირდა შეცდომა. გთხოვთ სცადოთ თავიდან.');

                                button.disabled = false;
                                buttonText.textContent = 'დასრულება';
                            }
                        })
                        .catch(error => {
                            console.log('Error:', error);
                            alert('დაფიქსირდა შეცდომა. გთხოვთ სცადოთ თავიდან.');

                            button.disabled = false;
                            buttonText.textContent = 'დასრულება';
                        });
                });
            }
        });
    </script>
@endpush
