@extends('layouts.dashboard')

@section('title', 'Kategori Pengaduan')
@section('page_title', 'Master Kategori Pengaduan')
@section('page_description', 'Kelola klasifikasi dan jenis pengaduan fasilitas serta masalah sekolah.')

@section('content')
<div class="space-y-6" x-data="{ modalCreate: false, editCat: null }">
    
    <div class="flex items-center justify-between">
        <div>
            <h3 class="font-bold text-base text-white">Daftar Kategori</h3>
            <p class="text-xs text-slate-400">Kategori menentukan alur disposisi pengaduan kepada petugas terkait.</p>
        </div>

        <button @click="modalCreate = true" class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg shadow-blue-600/30 transition">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Kategori Baru</span>
        </button>
    </div>

    <!-- Category Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($categories as $cat)
            <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-400 border border-blue-500/20 flex items-center justify-center font-bold">
                            <i data-lucide="{{ $cat->icon ?? 'folder' }}" class="w-5 h-5"></i>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700">
                            {{ $cat->complaints_count }} Tiket
                        </span>
                    </div>

                    <h4 class="text-base font-bold text-white">{{ $cat->name }}</h4>
                    <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">{{ $cat->description ?? 'Tidak ada deskripsi' }}</p>
                </div>

                <div class="pt-4 border-t border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-[11px] text-slate-500">Slug: {{ $cat->slug }}</span>
                    <form action="{{ route('dashboard.kategori.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-400 hover:text-rose-300 font-semibold p-1 hover:bg-rose-500/10 rounded-lg transition" title="Hapus Kategori">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal Create Category -->
    <div x-show="modalCreate" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-5" @click.outside="modalCreate = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h4 class="font-bold text-white text-base flex items-center gap-2">
                    <i data-lucide="folder-plus" class="w-5 h-5 text-blue-400"></i>
                    <span>Tambah Kategori Pengaduan</span>
                </h4>
                <button @click="modalCreate = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('dashboard.kategori.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Nama Kategori *</label>
                    <input type="text" name="name" required placeholder="Contoh: Keuangan & Pembayaran SPP" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Deskripsi Singkat</label>
                    <textarea name="description" rows="3" placeholder="Jelaskan jenis masalah yang termasuk dalam kategori ini..." class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Icon Lucide (Opsional)</label>
                    <input type="text" name="icon" placeholder="wrench, shield-alert, sparkles, book-open, lock" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="modalCreate = false" class="px-4 py-2 rounded-xl text-slate-400 hover:text-white text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-600/30">
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
