@props(['level' => 'h1'])

<{{ $level }} {{ $attributes->merge(['class' => 'font-title text-[#386641] uppercase tracking-wide']) }}>
    {{ $slot }}
</{{ $level }}>
