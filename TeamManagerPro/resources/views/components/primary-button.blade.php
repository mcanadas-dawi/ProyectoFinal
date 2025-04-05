@props(['color' => 'green'])

@php
$colors = [
    'green' => 'bg-[#6A994E] hover:brightness-110',
    'red' => 'bg-[#BC4749] hover:brightness-110',
    'gray' => 'bg-gray-500 hover:brightness-110',
];
@endphp

<button {{ $attributes->merge(['class' => $colors[$color] . ' text-white px-4 py-2 rounded-lg font-sans']) }}>
    {{ $slot }}
</button>