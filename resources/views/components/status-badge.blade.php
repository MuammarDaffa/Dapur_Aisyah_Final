@props(['status'])

@php
    $config = match($status) {
        'diproses' => [
            'label' => 'Diproses',
            'bg' => 'bg-blue-100',
            'text' => 'text-blue-800',
            'ring' => 'ring-blue-300',
            'dot' => 'bg-blue-500',
        ],
        'dikirim' => [
            'label' => 'Sedang Dikirim',
            'bg' => 'bg-purple-100',
            'text' => 'text-purple-800',
            'ring' => 'ring-purple-300',
            'dot' => 'bg-purple-500',
        ],
        'selesai' => [
            'label' => 'Selesai',
            'bg' => 'bg-green-100',
            'text' => 'text-green-800',
            'ring' => 'ring-green-300',
            'dot' => 'bg-green-500',
        ],
        'dibatalkan' => [
            'label' => 'Dibatalkan',
            'bg' => 'bg-red-100',
            'text' => 'text-red-800',
            'ring' => 'ring-red-300',
            'dot' => 'bg-red-500',
        ],
        'sudah_dibayar' => [
            'label' => 'Lunas',
            'bg' => 'bg-green-100',
            'text' => 'text-green-800',
            'ring' => 'ring-green-300',
            'dot' => 'bg-green-500',
        ],
        'belum_dibayar' => [
            'label' => 'Belum Bayar',
            'bg' => 'bg-yellow-100',
            'text' => 'text-yellow-800',
            'ring' => 'ring-yellow-300',
            'dot' => 'bg-yellow-500',
        ],
        'gagal' => [
            'label' => 'Gagal',
            'bg' => 'bg-red-100',
            'text' => 'text-red-800',
            'ring' => 'ring-red-300',
            'dot' => 'bg-red-500',
        ],
        default => [
            'label' => ucfirst(str_replace('_', ' ', $status)),
            'bg' => 'bg-gray-100',
            'text' => 'text-gray-800',
            'ring' => 'ring-gray-300',
            'dot' => 'bg-gray-500',
        ],
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold ring-1 {$config['bg']} {$config['text']} {$config['ring']}"]) }}>
    <span class="w-1.5 h-1.5 rounded-pill {{ $config['dot'] }} animate-pulse"></span>
    {{ $config['label'] }}
</span>
