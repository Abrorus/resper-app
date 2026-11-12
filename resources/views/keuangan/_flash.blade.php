{{-- Pesan hasil aksi: success, error, dan info --}}
@foreach (['success' => 'bg-[#e2f1ea] text-[#0e5f46] ring-[#12795a]/20', 'error' => 'bg-[#f9e5e3] text-[#a32c26] ring-[#c2362f]/20', 'info' => 'bg-[#fbf1d9] text-[#7a5300] ring-[#e6a417]/30'] as $jenis => $kelas)
    @if (session($jenis))
        <div role="{{ $jenis === 'success' ? 'status' : 'alert' }}" class="mb-5 flex items-start gap-2.5 rounded-xl px-4 py-3 ring-1 {{ $kelas }}">
            <svg class="mt-0.5 h-4.5 w-4.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                @if ($jenis === 'success')<path d="M20 6 9 17l-5-5"/>@else<circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16.5v.01"/>@endif
            </svg>
            <span>{{ session($jenis) }}</span>
        </div>
    @endif
@endforeach
