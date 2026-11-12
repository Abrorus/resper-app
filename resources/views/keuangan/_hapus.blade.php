{{-- Dialog konfirmasi hapus yang dipakai bersama oleh halaman Keuangan.
     Tombol pemicu: <button data-hapus="{{ url aksi }}" data-label="nama transaksi">. --}}
@push('modals')
<dialog id="dialog-hapus" aria-labelledby="judul-hapus" class="m-auto w-[min(420px,calc(100vw-32px))] rounded-2xl p-0 text-[#13211e] shadow-2xl backdrop:bg-[#0b1e19]/45">
    <form id="form-hapus" method="POST" class="flex flex-col gap-3 px-6.5 pb-5.5 pt-6.5">
        @csrf
        @method('DELETE')
        <h2 id="judul-hapus" class="font-['Bricolage_Grotesque',sans-serif] text-[1.3rem] font-bold">Hapus transaksi?</h2>
        <p class="text-[#5a6b66]"><b id="hapus-label" class="font-semibold text-[#13211e]"></b> akan dihapus dari laporan keuangan. Penghapusan tetap tercatat di riwayat koreksi.</p>
        <div class="mt-2 flex justify-end gap-2.5">
            <button type="button" data-tutup class="cursor-pointer rounded-lg border border-[#dce3e0] bg-white px-3.5 py-2 hover:border-[#13211e]">Batal</button>
            <button type="submit" class="cursor-pointer rounded-lg bg-[#c2362f] px-4.5 py-2.5 font-semibold text-white hover:bg-[#a32c26]">Hapus transaksi</button>
        </div>
    </form>
</dialog>
@endpush

@push('scripts')
<script>
(function () {
    const dialog = document.getElementById('dialog-hapus');
    document.addEventListener('click', e => {
        const tombol = e.target.closest('[data-hapus]');
        if (tombol) {
            document.getElementById('form-hapus').action = tombol.dataset.hapus;
            document.getElementById('hapus-label').textContent = tombol.dataset.label;
            dialog.showModal();
        }
        if (e.target.closest('[data-tutup]') || e.target === dialog) dialog.close();
    });
})();
</script>
@endpush
