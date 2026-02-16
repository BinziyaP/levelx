@extends('layouts.seller')

@section('content')
    <h3 class="text-gray-700 text-3xl font-medium mb-4">Notifications</h3>

    <div class="mt-8">
        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($notifications as $notification)
                    <li class="{{ $notification->read_at ? 'opacity-75' : 'bg-indigo-50 dark:bg-gray-700' }}">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-indigo-600 truncate">
                                    {{ $notification->data['message'] }}
                                </p>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $notification->read_at ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $notification->read_at ? 'Read' : 'New' }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                    @if(!$notification->read_at)
                                        <form action="{{ route('seller.notifications.read', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 border border-indigo-600 rounded px-2 py-1 text-xs">
                                                Mark as Read
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li>
                        <div class="px-4 py-4 sm:px-6 text-center text-gray-500">
                            No notifications.
                        </div>
                    </li>
                @endforelse
            </ul>
             <div class="px-6 py-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
@endsection
