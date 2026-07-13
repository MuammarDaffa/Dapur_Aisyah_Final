@extends('layouts.app')
@section('title', 'Notifikasi')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
        <svg class="w-7 h-7 text-orange-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
        <span><span class="text-orange-500">Notifikasi</span></span>
    </h2>
    <div class="space-y-3">
        @forelse($notifications as $notification)
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 {{ $notification->read_at ? 'opacity-60' : '' }}">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium text-gray-900">{{ $notification->data['title'] ?? 'Notifikasi' }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                        <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$notification->read_at)
                        <form action="{{ route('customer.notifications.read', $notification->id) }}" method="POST">
                            @csrf
                            <button class="text-xs text-orange-500 hover:text-orange-600">Tandai Dibaca</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-gray-500">
                <div class="w-16 h-16 mx-auto mb-3 text-gray-300 flex items-center justify-center">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <p>Belum ada notifikasi.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-6">{{ $notifications->links() }}</div>
</div>
@endsection
