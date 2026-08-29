@if (session('success') || session('error') || $errors->any())
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 5000)" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         class="fixed top-20 right-5 z-50 max-w-sm w-full"
         x-cloak>
        
        @if (session('success'))
            <div class="bg-zinc-950/95 text-zinc-100 backdrop-blur-md border border-zinc-800 rounded-lg p-4 shadow-xl flex items-start gap-3">
                <div class="p-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-md shrink-0 mt-0.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                </div>
                <div class="flex-1 text-xs">
                    <div class="font-semibold text-zinc-100">Berhasil</div>
                    <div class="text-zinc-400 mt-0.5 leading-relaxed">{{ session('success') }}</div>
                </div>
                <button @click="show = false" class="text-zinc-500 hover:text-zinc-300 p-0.5 transition-colors">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-zinc-950/95 text-zinc-100 backdrop-blur-md border border-zinc-800 rounded-lg p-4 shadow-xl flex items-start gap-3">
                <div class="p-1 bg-red-500/10 text-red-400 border border-red-500/20 rounded-md shrink-0 mt-0.5">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                </div>
                <div class="flex-1 text-xs">
                    <div class="font-semibold text-zinc-100">Perhatian</div>
                    <div class="text-zinc-400 mt-0.5 leading-relaxed">{{ session('error') }}</div>
                </div>
                <button @click="show = false" class="text-zinc-500 hover:text-zinc-300 p-0.5 transition-colors">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-zinc-950/95 text-zinc-100 backdrop-blur-md border border-zinc-800 rounded-lg p-4 shadow-xl flex items-start gap-3">
                <div class="p-1 bg-orange-500/10 text-orange-400 border border-orange-500/20 rounded-md shrink-0 mt-0.5">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                </div>
                <div class="flex-1 text-xs">
                    <div class="font-semibold text-zinc-100">Kesalahan Input:</div>
                    <ul class="text-zinc-400 mt-1 list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="text-zinc-500 hover:text-zinc-300 p-0.5 transition-colors">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        @endif

    </div>
@endif
