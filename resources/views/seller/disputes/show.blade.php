@extends('layouts.seller')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-gray-700 dark:text-gray-200 text-3xl font-medium">Return #{{ $dispute->id }}</h3>
            <p class="text-sm text-gray-500 mt-1">Order #{{ $dispute->order_id }} &middot; {{ $dispute->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <a href="{{ route('seller.disputes.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">← Back to Returns</a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Buyer Complaint --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h4 class="font-bold text-lg mb-3 text-gray-800 dark:text-gray-200">Buyer Return Request</h4>
                <div class="space-y-2">
                    <p class="text-sm"><span class="font-medium text-gray-500">Reason:</span> <span class="text-gray-900 dark:text-gray-100">{{ $dispute->reason }}</span></p>
                    <p class="text-sm"><span class="font-medium text-gray-500">Description:</span></p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 p-3 rounded">{{ $dispute->description }}</p>
                </div>
            </div>

            {{-- Evidence Files --}}
            @if($dispute->evidences->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h4 class="font-bold text-lg mb-3 text-gray-800 dark:text-gray-200">Buyer Evidence</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($dispute->evidences as $evidence)
                        @if($evidence->file_type === 'image')
                            <a href="{{ asset('storage/' . $evidence->file_path) }}" target="_blank" class="block rounded-lg border overflow-hidden hover:shadow-lg transition">
                                <img src="{{ asset('storage/' . $evidence->file_path) }}" alt="Evidence" class="w-full h-32 object-cover">
                            </a>
                        @else
                            <a href="{{ asset('storage/' . $evidence->file_path) }}" target="_blank" class="flex items-center justify-center h-32 bg-gray-50 dark:bg-gray-700 rounded-lg border hover:shadow-lg transition">
                                <div class="text-center">
                                    <span class="text-3xl">📄</span>
                                    <p class="text-xs text-gray-500 mt-1">PDF Document</p>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Seller Response Form --}}
            @if(in_array($dispute->status, ['pending', 'under_review']))
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h4 class="font-bold text-lg mb-3 text-gray-800 dark:text-gray-200">Your Response</h4>
                <form action="{{ route('seller.disputes.respond', $dispute->id) }}" method="POST">
                    @csrf
                    <textarea name="response" rows="4" required maxlength="2000" placeholder="Write your response to this return request..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"></textarea>
                    <div class="mt-3 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none transition">
                            Submit Response
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Status Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h4 class="font-bold text-sm mb-3 text-gray-500 uppercase tracking-wider">Status</h4>
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                    {{ $dispute->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $dispute->status === 'under_review' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $dispute->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $dispute->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ ucfirst(str_replace('_', ' ', $dispute->status)) }}
                </span>

                @if($dispute->resolution_type)
                    <div class="mt-3 text-sm">
                        <p class="text-gray-500">Resolution: <span class="text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $dispute->resolution_type)) }}</span></p>
                        @if($dispute->refund_amount > 0)
                            <p class="text-green-600 font-semibold mt-1">Refund: ₹{{ number_format($dispute->refund_amount, 2) }}</p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Timeline --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h4 class="font-bold text-sm mb-3 text-gray-500 uppercase tracking-wider">Timeline</h4>
                <div class="space-y-4">
                    @foreach($dispute->logs as $log)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full
                                {{ $log->new_status === 'pending' ? 'bg-yellow-400' : '' }}
                                {{ $log->new_status === 'under_review' ? 'bg-blue-400' : '' }}
                                {{ $log->new_status === 'resolved' ? 'bg-green-400' : '' }}
                                {{ $log->new_status === 'rejected' ? 'bg-red-400' : '' }}">
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $log->note }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $log->created_at->format('d M Y, h:i A') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
