@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h3 class="text-gray-700 text-3xl font-medium">Sellers Management</h3>

    <div class="mt-8">
        <div class="flex flex-col">
            <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach($sellers as $seller)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm leading-5 font-medium text-gray-900">{{ $seller->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm leading-5 text-gray-500">{{ $seller->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2 mr-3 inline-block">
                                    {{-- Accept Button --}}
                                    <form action="{{ route('admin.sellers.approve', $seller->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 rounded-l-md text-sm font-medium {{ $seller->status === 'active' ? 'bg-green-600 text-white cursor-default' : 'bg-gray-200 text-gray-700 hover:bg-green-200' }}" {{ $seller->status === 'active' ? 'disabled' : '' }}>
                                            Accept
                                        </button>
                                    </form>

                                    {{-- Decline Button --}}
                                    <form action="{{ route('admin.sellers.decline', $seller->id) }}" method="POST" class="inline-block -ml-1">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 rounded-r-md text-sm font-medium {{ $seller->status === 'declined' ? 'bg-red-600 text-white cursor-default' : 'bg-gray-200 text-gray-700 hover:bg-red-200' }}" {{ $seller->status === 'declined' ? 'disabled' : '' }}>
                                            Decline
                                        </button>
                                    </form>
                                </div>
                                
                                <form action="{{ route('admin.sellers.delete', $seller->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure? This will delete the seller and all their products.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Delete</button>
                                </form>
                            </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
