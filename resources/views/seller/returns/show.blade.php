@extends('layouts.seller')

@section('content')
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Return Details: #RET-') . $return->id }}
            </h2>
            <a href="{{ route('seller.returns.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">&larr; Back to List</a>
        </div>
    </div>

    <div class="mt-4">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Case Information -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium mb-4">Return Information</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Order ID</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">#{{ $return->order->id }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Current Status</p>
                                <p class="font-medium">
                                     @php
                                        $color = match($return->status) {
                                            'pending' => 'text-yellow-600',
                                            'under_review' => 'text-blue-600',
                                            'resolved' => 'text-green-600',
                                            'rejected' => 'text-red-600',
                                            default => 'text-gray-600',
                                        };
                                    @endphp
                                    <span class="{{ $color }}">{{ ucfirst(str_replace('_', ' ', $return->status)) }}</span>
                                </p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-gray-500">Buyer's Reason</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $return->reason }}</p>
                            </div>
                        </div>

                        @if($return->evidences->isNotEmpty())
                            <div class="mt-6 border-t pt-4">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Evidence Provided by Buyer</h4>
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach($return->evidences as $evidence)
                                        <a href="{{ Storage::url($evidence->file_path) }}" target="_blank" class="block border rounded p-1 hover:border-indigo-500 transition">
                                            @if(Str::startsWith($evidence->file_type, 'image/'))
                                                <img src="{{ Storage::url($evidence->file_path) }}" class="h-20 w-full object-cover rounded" alt="Evidence">
                                            @else
                                                <div class="h-20 w-full flex items-center justify-center bg-gray-100 text-xs text-gray-500">File</div>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Seller Response Form (Only for active cases) --}}
                    @if(!in_array($return->status, ['resolved', 'rejected', 'approved']))
                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium mb-4 text-indigo-600">Add Your Response</h3>
                            <form action="{{ route('seller.returns.respond', $return->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Message to Admin/Buyer</label>
                                    <textarea name="message" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Provide your side of the story or any clarifications..." required></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <x-primary-button>Submit Response</x-primary-button>
                                </div>
                            </form>
                        </div>
                    @else
                         <div class="bg-gray-50 dark:bg-gray-900/40 shadow sm:rounded-lg p-6 border italic text-center text-gray-500">
                            This case is closed. Responses are no longer accepted.
                        </div>
                    @endif
                </div>

                <!-- Timeline / Logs -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium mb-4">Case History</h3>
                    <div class="relative">
                        <div class="absolute left-4 top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700"></div>
                        <div class="space-y-6">
                            @foreach($return->logs->sortByDesc('created_at') as $log)
                                <div class="relative pl-10">
                                    <div class="absolute left-2.5 top-1.5 w-3 h-3 rounded-full bg-indigo-500 border-2 border-white dark:border-gray-800"></div>
                                    <div class="text-xs text-gray-500 mb-1">
                                        {{ $log->created_at->format('M d, H:i') }}
                                        @if($log->changer)
                                            by <span class="font-medium">
                                                @if($log->changer->role === 'admin') Admin @elseif($log->changer->id === auth()->id()) You @else Buyer @endif
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $log->new_status !== $log->old_status ? 'Status shifted to ' . ucfirst(str_replace('_', ' ', $log->new_status)) : 'New Response' }}
                                    </div>
                                    @if($log->note)
                                        <div class="mt-1 p-2 bg-gray-50 dark:bg-gray-900 border rounded text-xs text-gray-600 dark:text-gray-400 italic">
                                            {{ $log->note }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
