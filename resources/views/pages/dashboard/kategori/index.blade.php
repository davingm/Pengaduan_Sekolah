@extends('layouts.dashboard')

@section('title', 'Kategori Pengaduan')
@section('page_title', 'Master Kategori')

@section('content')
<div class="space-y-6" x-data="{ modalCreate: false }">
    
    <div class="flex items-center justify-between pb-2 border-b border-zinc-800/80">
        <div>
            <h3 class="font-semibold text-xs text-zinc-100 font-sans">Daftar Kategori</h3>
            <p class="text-[11px] text-zinc-500 font-mono">Klasifikasi dan penugasan penanganan pengaduan</p>
        </div>

        <button @click="modalCreate = true" 
                class="inline-flex items-center gap-1.5 bg-zinc-100 hover:bg-white text-zinc-950 text-xs font-medium px-3.5 py-1.5 rounded-md shadow-xs transition-all">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            <span>Tambah Kategori</span>
        </button>
    </div>

    <!-- Category Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($categories as $cat)
            <div class="cf-card p-4 hover:border-zinc-700/80 transition-colors flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-8 h-8 rounded-md bg-[#18181b] border border-zinc-800 flex items-center justify-center text-zinc-300">
                            <i data-lucide="{{ $cat->icon ?? 'folder' }}" class="w-4 h-4"></i>
                        </div>
                        <span class="text-[10px] font-mono font-medium px-2 py-0.5 rounded-md bg-[#121215] text-zinc-400 border border-zinc-800">
                            {{ $cat->complaints_count }} Tiket
                        </span>
                    </div>

                    <div>
                        <h4 class="text-xs font-semibold text-zinc-100">{{ $cat->name }}</h4>
                        <p class="text-[11px] text-zinc-400 mt-1 leading-relaxed">{{ $cat->description ?? 'Tidak ada deskripsi' }}</p>
                    </div>
                </div>

                <div class="pt-3 border-t border-zinc-800/80 flex items-center justify-between text-xs">
                    <span class="font-mono text-[10px] text-zinc-500">slug: {{ $cat->slug }}</span>
                    <form action="{{ route('dashboard.kategori.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-zinc-500 hover:text-red-400 p-1 rounded-md hover:bg-red-500/10 transition-colors" title="Hapus Kategori">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal Create Category -->
    <div x-show="modalCreate" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-[#0c0c0e] border border-zinc-800 rounded-xl p-5 max-w-md w-full shadow-2xl space-y-4" @click.outside="modalCreate = false">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
                <h4 class="font-semibold text-zinc-100 text-xs flex items-center gap-2">
                    <i data-lucide="folder-plus" class="w-4 h-4 text-zinc-300"></i>
                    <span>Tambah Kategori Baru</span>
                </h4>
                <button @click="modalCreate = false" class="text-zinc-500 hover:text-zinc-300 p-1"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>

            <form action="{{ route('dashboard.kategori.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-zinc-300 mb-1.5">Nama Kategori *</label>
                    <input type="text" name="name" required placeholder="Contoh: Sarana & Prasarana Kelas" class="w-full bg-[#121215] border border-zinc-800 rounded-md px-3 py-1.5 text-xs text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:border-zinc-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-300 mb-1.5">Deskripsi Singkat</label>
                    <textarea name="description" rows="3" placeholder="Jelaskan jenis laporan yang termasuk kategori ini..." class="w-full bg-[#121215] border border-zinc-800 rounded-md p-2.5 text-xs text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:border-zinc-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-300 mb-1.5">Icon Lucide (Opsional)</label>
                    <input type="text" name="icon" placeholder="wrench, shield-alert, sparkles, book-open, lock" class="w-full bg-[#121215] border border-zinc-800 rounded-md px-3 py-1.5 text-xs text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:border-zinc-500 font-mono">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-zinc-800">
                    <button type="button" @click="modalCreate = false" class="px-3 py-1.5 rounded-md text-zinc-400 hover:text-zinc-200 text-xs transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 rounded-md bg-zinc-100 hover:bg-white text-zinc-950 font-medium text-xs shadow-sm transition">
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
