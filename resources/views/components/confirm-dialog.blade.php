@props(['id' => 'confirm-dialog', 'title' => 'Are you sure?', 'message' => 'This action cannot be undone.', 'confirmUrl' => '#', 'confirmLabel' => 'Confirm', 'type' => 'danger'])

<div id="{{ $id }}" x-data="{ open: false }" x-show="open" x-cloak x-transition.opacity
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
     @confirm-dialog.window="open = true; $nextTick(() => { if($el.dataset.url) confirmUrl = $el.dataset.url })">
    <div @click.away="open = false" x-show="open" x-transition
         class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <div class="flex items-center gap-3 mb-4">
            @if($type === 'danger')
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            @else
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            @endif
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                <p class="text-sm text-gray-500">{{ $message }}</p>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 mt-6">
            <button @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">Cancel</button>
            <form method="POST" :action="confirmUrl || '{{ $confirmUrl }}'">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white {{ $type === 'danger' ? 'bg-red-600 hover:bg-red-700' : 'bg-primary-600 hover:bg-primary-700' }} rounded-lg transition-colors">
                    {{ $confirmLabel }}
                </button>
            </form>
        </div>
    </div>
</div>
