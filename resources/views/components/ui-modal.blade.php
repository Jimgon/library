@props(['id' => 'modal', 'title' => 'Modal Title'])

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden items-center justify-center">
  <div class="fixed inset-0 bg-black/50"></div>
  <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-4 z-10">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-lg font-semibold">{{ $title }}</h3>
      <button type="button" class="text-gray-500" onclick="document.getElementById('{{ $id }}').classList.add('hidden')">✕</button>
    </div>
    <div class="modal-body">
      {{ $slot }}
    </div>
  </div>
</div>
