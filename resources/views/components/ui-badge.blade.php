@props(['color' => 'green'])

@php
  $map = [
    'green' => 'bg-green-600 text-white',
    'red' => 'bg-red-600 text-white',
    'yellow' => 'bg-yellow-400 text-black',
    'gray' => 'bg-gray-300 text-black',
  ];
  $cls = $map[$color] ?? $map['green'];
@endphp

<span {{ $attributes->merge(['class' => 'px-2 py-0.5 rounded-full text-sm font-semibold ' . $cls]) }}>{{ $slot }}</span>
