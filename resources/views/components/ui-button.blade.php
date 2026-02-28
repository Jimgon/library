@props(['variant' => 'primary', 'type' => 'button'])

@php
  $base = 'inline-flex items-center px-3 py-1.5 rounded text-sm font-medium focus:outline-none';
  $colors = [
    'primary' => 'bg-black text-white hover:bg-gray-800',
    'danger' => 'bg-red-600 text-white hover:bg-red-700',
    'warning' => 'bg-yellow-400 text-black hover:bg-yellow-500',
    'success' => 'bg-green-600 text-white hover:bg-green-700',
  ];
  $classes = $base . ' ' . ($colors[$variant] ?? $colors['primary']);
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
  {{ $slot }}
</button>
