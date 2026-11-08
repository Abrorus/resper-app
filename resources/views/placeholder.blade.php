@extends('layouts.app')

@section('title', $judul ?? $modul)

@section('content')
    <p class="mb-1.5 text-[0.9rem] text-[#5a6b66]">{{ $modul }}</p>
    <h1 class="font-['Bricolage_Grotesque',sans-serif] text-[2.1rem] font-bold leading-tight tracking-tight">{{ $judul ?? $modul }}</h1>

    <div class="mt-7 grid min-h-[320px] place-items-center rounded-2xl border border-dashed border-[#dce3e0] bg-white p-8 text-center text-[#5a6b66]">
        <p>Halaman ini sedang dikerjakan.</p>
    </div>
@endsection