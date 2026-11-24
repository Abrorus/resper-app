{{-- Field formulir Transaksi Pengeluaran, dipakai oleh halaman tambah dan koreksi --}}
@php
    $kelasInput = 'w-full rounded-lg border border-[#dce3e0] bg-white px-3 py-2.5 outline-none focus:border-[#0b3d31] focus:ring-3 focus:ring-[#0b3d31]/12 aria-invalid:border-[#c2362f] aria-invalid:ring-3 aria-invalid:ring-[#f9e5e3]';
@endphp

@if ($errors->any())
    <div role="alert" class="rounded-xl bg-[#f9e5e3] px-4 py-3 text-[#a32c26] ring-1 ring-[#c2362f]/20">
        <p class="font-semibold">Data belum bisa disimpan. Perbaiki {{ $errors->count() }} isian berikut:</p>
        <ul class="mt-1 list-disc pl-5">
            @foreach ($errors->all() as $pesan)<li>{{ $pesan }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="grid gap-4 md:grid-cols-2">
    <label class="flex flex-col gap-1.5">
        <span class="text-[0.92rem] font-medium">Kategori</span>
        <select name="category" @error('category') aria-invalid="true" @enderror class="{{ $kelasInput }}">
            <option value="">Pilih kategori</option>
            @foreach ($categories as $k)
                <option value="{{ $k }}" @selected(old('category', $expense->category) === $k)>{{ $k }}</option>
            @endforeach
        </select>
        @error('category')<span class="text-[0.85rem] text-[#c2362f]">{{ $message }}</span>@enderror
    </label>

    <label class="flex flex-col gap-1.5">
        <span class="text-[0.92rem] font-medium">Tanggal transaksi</span>
        <input type="date" name="transaction_date" max="{{ now()->toDateString() }}"
               value="{{ old('transaction_date', $expense->transaction_date?->toDateString()) }}"
               @error('transaction_date') aria-invalid="true" @enderror class="{{ $kelasInput }}">
        @error('transaction_date')<span class="text-[0.85rem] text-[#c2362f]">{{ $message }}</span>@enderror
    </label>

    <label class="flex flex-col gap-1.5 md:col-span-2">
        <span class="text-[0.92rem] font-medium">Nominal</span>
        <span class="flex items-center rounded-lg border border-[#dce3e0] bg-white focus-within:border-[#0b3d31] focus-within:ring-3 focus-within:ring-[#0b3d31]/12 has-aria-invalid:border-[#c2362f] has-aria-invalid:ring-3 has-aria-invalid:ring-[#f9e5e3]">
            <span class="pl-3 font-semibold text-[#5a6b66]">Rp</span>
            <input type="number" name="amount" inputmode="numeric" min="1" step="1" placeholder="0"
                   value="{{ old('amount', $expense->amount) }}"
                   @error('amount') aria-invalid="true" @enderror
                   class="w-full bg-transparent px-2.5 py-2.5 text-[1.1rem] font-semibold tabular-nums outline-none">
        </span>
        @error('amount')<span class="text-[0.85rem] text-[#c2362f]">{{ $message }}</span>@enderror
    </label>

    <label class="flex flex-col gap-1.5 md:col-span-2">
        <span class="text-[0.92rem] font-medium">Keterangan <span class="font-normal text-[#5a6b66]">(opsional)</span></span>
        <textarea name="description" rows="3" maxlength="255" placeholder="Contoh: belanja sayur dan daging di pasar"
                  @error('description') aria-invalid="true" @enderror class="{{ $kelasInput }}">{{ old('description', $expense->description) }}</textarea>
        @error('description')<span class="text-[0.85rem] text-[#c2362f]">{{ $message }}</span>@enderror
    </label>
</div>
