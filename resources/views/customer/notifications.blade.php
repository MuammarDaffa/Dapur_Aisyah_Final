@extends('layouts.app')
@section('title', 'Notifikasi')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">🔔 <span class="text-orange-500">Notifikasi</span></h2>
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
                <p class="text-4xl mb-3">🔕</p>
                <p>Belum ada notifikasi.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-6">{{ $notifications->links() }}</div>
</div>
@endsection
