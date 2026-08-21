@if (session('success') || session('error') || $errors->any())
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 6000)" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed top-20 right-4 z-50 max-w-md w-full"
         x-cloak>
        
        @if (session('success'))
            <div class="bg-emerald-900/90 text-white backdrop-blur-xl border border-emerald-500/40 rounded-2xl p-4 shadow-2xl flex items-start gap-3">
                <div class="p-1 bg-emerald-500/20 text-emerald-400 rounded-lg shrink-0 mt-0.5">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <div class="flex-1 text-sm">
                    <div class="font-bold text-emerald-300">Berhasil!</div>
                    <div class="text-emerald-100 text-xs mt-0.5">{{ session('success') }}</div>
                </div>
                <button @click="show = false" class="text-emerald-300 hover:text-white p-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-rose-900/90 text-white backdrop-blur-xl border border-rose-500/40 rounded-2xl p-4 shadow-2xl flex items-start gap-3">
                <div class="p-1 bg-rose-500/20 text-rose-400 rounded-lg shrink-0 mt-0.5">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </div>
                <div class="flex-1 text-sm">
                    <div class="font-bold text-rose-300">Perhatian</div>
                    <div class="text-rose-100 text-xs mt-0.5">{{ session('error') }}</div>
                </div>
                <button @click="show = false" class="text-rose-300 hover:text-white p-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-rose-900/90 text-white backdrop-blur-xl border border-rose-500/40 rounded-2xl p-4 shadow-2xl flex items-start gap-3">
                <div class="p-1 bg-rose-500/20 text-rose-400 rounded-lg shrink-0 mt-0.5">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div class="flex-1 text-sm">
                    <div class="font-bold text-rose-300">Terdapat kesalahan input:</div>
                    <ul class="text-rose-100 text-xs mt-1 list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="text-rose-300 hover:text-white p-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

    </div>
@endif
