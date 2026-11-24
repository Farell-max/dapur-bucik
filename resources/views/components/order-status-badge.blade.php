@props(['status'])

@php
    $map = [
        'menunggu_konfirmasi' => [
            'label' => 'MENUNGGU KONFIRMASI',
            'classes' => 'border-[#E29E31] text-[#CC8A22] bg-[#FAF4E8]/60',
        ],
        'dimasak' => [
            'label' => 'DIMASAK',
            'classes' => 'border-[#E29E31] text-[#CC8A22] bg-[#FAF4E8]/60',
        ],
        'siap_diambil' => [
            'label' => 'SIAP DIAMBIL',
            'classes' => 'border-[#1B362D] text-[#1B362D] bg-[#FAF4E8]/60',
        ],
        'menunggu_driver' => [
            'label' => 'MENUNGGU DRIVER',
            'classes' => 'border-[#1B362D] text-[#1B362D] bg-[#FAF4E8]/60',
        ],
        'selesai' => [
            'label' => 'SELESAI',
            'classes' => 'border-[#1B362D] text-[#1B362D] bg-white',
        ],
        'ditolak' => [
            'label' => 'DITOLAK',
            'classes' => 'border-red-600 text-red-700 bg-red-50/70',
        ],
        'habis' => [
            'label' => 'HABIS',
            'classes' => 'border-red-500 text-red-600 bg-white',
        ],
        'diblokir' => [
            'label' => 'DIBLOKIR',
            'classes' => 'border-red-500 text-red-600 bg-white',
        ],
    ];

    $meta = $map[$status] ?? [
        'label' => mb_strtoupper(str_replace('_', ' ', (string) $status)),
        'classes' => 'border-[#1B362D] text-[#1B362D] bg-white',
    ];
@endphp

<span {{ $attributes->merge([
    'class' => "bucik-stamp {$meta['classes']}",
]) }}>
    {{ $meta['label'] }}
</span>
