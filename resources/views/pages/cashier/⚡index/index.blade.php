<div>
    <livewire:header.index />
    
    <!-- Alert Component -->
    <div x-data="{ show: false, message: '', type: 'success' }" 
         x-init="$wire.on('alert', (data) => { 
             show = true; 
             message = data.message; 
             type = data.type; 
             setTimeout(() => show = false, 2500) 
         })" 
         x-show="show" 
         x-transition 
         class="fixed top-5 right-5 z-50 w-[90%] sm:w-auto max-w-[350px]" 
         style="display: none;">
        <div class="px-4 py-3 rounded-lg shadow-lg flex items-center gap-2" 
             :class="{
                 'bg-green-500': type === 'success',
                 'bg-red-500': type === 'error',
                 'bg-yellow-500': type === 'warning',
                 'bg-blue-500': type === 'info'
             }">
            <span class="material-symbols-outlined text-white text-sm flex-shrink-0">
                <template x-if="type === 'success'">check</template>
                <template x-if="type === 'error'">close</template>
                <template x-if="type === 'warning'">warning</template>
                <template x-if="type === 'info'">info</template>
            </span>
            <span class="text-white text-sm font-medium break-words" x-text="message"></span>
        </div>
    </div>

    <!-- Session Alerts -->
    @if (session()->has('success'))
    <div x-data="{ show: true }" 
         x-init="setTimeout(() => show = false, 2500)" 
         x-show="show" 
         x-transition
         class="fixed top-5 right-5 z-50 w-[90%] sm:w-auto max-w-[350px] bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-2">
        <span class="material-symbols-outlined text-white text-sm flex-shrink-0">check</span>
        <span class="text-sm font-medium break-words">{{ session('success') }}</span>
    </div>
    @endif

    @if (session()->has('error'))
    <div x-data="{ show: true }" 
         x-init="setTimeout(() => show = false, 2500)" 
         x-show="show" 
         x-transition
         class="fixed top-5 right-5 z-50 w-[90%] sm:w-auto max-w-[350px] bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-2">
        <span class="material-symbols-outlined text-white text-sm flex-shrink-0">close</span>
        <span class="text-sm font-medium break-words">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Scanner Modal -->
    <div id="scannerModal" 
         class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[999] p-4">
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 text-white w-full max-w-lg rounded-xl shadow-xl p-4 relative">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-lg font-bold">Scan Barcode</h2>
                <button id="closeScanner" class="text-gray-300 hover:text-white text-xl p-2">
                    ✕
                </button>
            </div>
            <div id="reader" class="w-full rounded-xl overflow-hidden"></div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex flex-col-reverse lg:flex-row h-screen overflow-hidden bg-slate-50 dark:bg-[#111a22]">
        <!-- Products Section -->
        <section class="flex-1 flex flex-col h-full min-w-0 overflow-hidden order-2 lg:order-1">
            <!-- Sticky Header -->
            <div class="sticky top-0 z-10 bg-white/80 dark:bg-[#111a22]/95 backdrop-blur-md border-b p-3 sm:p-4 space-y-3 sm:space-y-4">
                <!-- Search Bar -->
                <div class="relative w-full flex gap-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 sm:pl-4 text-slate-400">
                            <span class="material-symbols-outlined text-xl sm:text-2xl">search</span>
                        </div>
                        <input wire:model.live.debounce.200ms="search" 
                               id="search-input" 
                               type="text"
                               placeholder="Search products..."
                               class="w-full h-10 sm:h-12 pl-10 sm:pl-12 pr-3 sm:pr-4 rounded-xl bg-slate-100 dark:bg-[#233648] border-0 focus:ring-2 focus:ring-primary text-sm sm:text-base" />
                    </div>
                    <button onclick="startScan()"
                            class="p-2 sm:p-3 bg-primary text-white rounded-xl hover:bg-blue-600 transition-all active:scale-[0.98] flex-shrink-0">
                        <span class="material-symbols-outlined text-2xl sm:text-2xl">qr_code_scanner</span>
                    </button>
                </div>

                <!-- Categories -->
                <div class="flex gap-2 sm:gap-3 overflow-x-auto no-scrollbar pb-1">
                    <button wire:click="$set('activeCategory', 'all')"
                            class="flex items-center px-3 sm:px-5 h-8 sm:h-9 rounded-full whitespace-nowrap {{ $activeCategory == 'all' ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-[#233648] text-slate-600' }} text-xs sm:text-sm font-semibold transition-all">
                        All Items
                    </button>
                    @foreach ($categories as $item)
                    <button wire:click="$set('activeCategory', {{ $item->id }})"
                            class="flex items-center px-3 sm:px-5 h-8 sm:h-9 rounded-full whitespace-nowrap active:scale-[0.98] {{ $activeCategory == $item->id ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-[#233648] text-slate-600' }} text-xs sm:text-sm font-semibold transition-all">
                        {{ $item->name }}
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Products Grid -->
            <div class="flex-1 overflow-y-auto p-3 sm:p-4 md:p-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2 sm:gap-3 md:gap-4">
                    @if ($products->count() == 0)
                    <div class="col-span-full text-center py-6 sm:py-10">
                        <p class="text-slate-500 dark:text-slate-400 text-sm sm:text-base">No products found for matching "{{ $search }}".</p>
                    </div>
                    @endif
                    
                    @foreach ($products as $product)
                    <div wire:click="addToCart({{ $product->id }})" 
                         wire:key="prod-{{ $product->id }}"
                         class="group relative flex active:scale-[0.98] flex-col bg-white dark:bg-[#192633] rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all border cursor-pointer 
                         {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed pointer-events-none border-red-300 dark:border-red-800' : '' }}">
                        
                        <!-- Product Image -->
                        <div class="aspect-[4/3] w-full bg-slate-200 relative overflow-hidden">
                            <div class="absolute inset-0 bg-cover bg-center transition-transform group-hover:scale-110
                                {{ $product->stock <= 0 ? 'grayscale' : '' }}"
                                style="background-image: url('{{ asset('storage/'.$product->image_path) }}');">
                            </div>

                            <!-- Stock Badge -->
                            <div class="absolute top-1 right-1 sm:top-2 sm:right-2 {{ $product->stock <= 0 ? 'bg-red-500' : 'bg-black/60' }} text-white text-[10px] sm:text-xs font-bold px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md">
                                @if($product->stock <= 0)
                                <span class="flex items-center gap-0.5 sm:gap-1">
                                    <span class="material-symbols-outlined text-[10px] sm:text-xs">block</span>
                                    <span class="hidden xs:inline">Out of Stock</span>
                                </span>
                                @else
                                <span class="whitespace-nowrap">{{ $product->stock }} left</span>
                                @endif
                            </div>

                            @if($product->stock <= 0)
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                <span class="bg-red-500 text-white px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold rotate-[-15deg] shadow-lg whitespace-nowrap">
                                    SOLD OUT
                                </span>
                            </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="p-2 sm:p-3">
                            <h3 class="font-bold text-slate-900 dark:text-white leading-tight line-clamp-1 text-xs sm:text-sm">
                                {{ $product->name }}
                            </h3>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-primary font-bold text-xs sm:text-sm {{ $product->stock <= 0 ? 'text-red-400 dark:text-red-500' : '' }}">
                                    Rp{{ number_format($product->price, 0, ',', '.') }}
                                </span>
                                <div class="size-6 sm:size-8 rounded-lg {{ $product->stock <= 0 ? 'bg-slate-300 dark:bg-slate-600 text-slate-500' : 'bg-slate-100 dark:bg-[#233648] text-primary group-hover:bg-primary group-hover:text-white' }} flex items-center justify-center">
                                    <span class="material-symbols-outlined text-sm sm:text-base">
                                        {{ $product->stock <= 0 ? 'block' : 'add' }}
                                    </span>
                                </div>
                            </div>

                            @if($product->stock <= 0)
                            <div class="mt-1 sm:mt-2 text-[10px] sm:text-xs text-red-500 flex items-center gap-0.5 sm:gap-1">
                                <span class="material-symbols-outlined text-[12px] sm:text-[14px]">info</span>
                                <span class="truncate">Tidak tersedia</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    <!-- Infinite Scroll Observer -->
                    <div x-data="{
                        observe() {
                            let observer = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                    if (entry.isIntersecting && @js($products->hasMorePages())) {
                                        @this.call('loadMore')
                                    }
                                })
                            }, { threshold: 0.1 })
                            observer.observe($el)
                        }
                    }" x-init="observe" class="h-10 w-full flex justify-center items-center p-4 col-span-full">
                        <div wire:loading wire:target="loadMore">
                            <svg class="animate-spin h-5 w-5 sm:h-6 sm:w-6 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cart Sidebar -->
        <aside class="flex flex-col w-full lg:w-[380px] xl:w-[400px] flex-none bg-white dark:bg-[#192633] border-t lg:border-t-0 lg:border-l border-slate-200 dark:border-[#233648] shadow-2xl z-20 order-1 lg:order-2">
            <!-- Cart Header -->
            <div class="p-3 sm:p-4 border-b border-slate-200 dark:border-[#233648]">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs sm:text-sm font-semibold text-slate-500 uppercase">Current Order</h3>
                    <button wire:click="clearAll" class="text-primary hover:text-blue-400 text-xs sm:text-sm font-medium">Clear All</button>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="h-30 md:h-auto md:flex-1 overflow-y-auto p-3 sm:p-4 space-y-3">
                @forelse($cart as $id => $item)
                <div class="flex gap-2 sm:gap-3 items-start group" wire:key="cart-{{ $id }}">
                    <div class="size-12 sm:size-16 rounded-lg bg-slate-100 bg-cover bg-center flex-none"
                         style="background-image: url({{ asset('storage/'.$item['image_path']) }});">
                    </div>
                    <div class="flex-1 flex flex-col justify-between min-w-0">
                        <div class="flex justify-between items-start gap-1">
                            <h4 class="font-medium text-slate-900 dark:text-white leading-tight line-clamp-1 text-xs sm:text-sm">
                                {{ $item['name'] }}
                            </h4>
                            <span class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm whitespace-nowrap">
                                Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <div class="text-[10px] sm:text-xs text-slate-500 truncate">
                                Rp{{ number_format($item['price'], 0, ',', '.') }}/unit
                            </div>
                            <div class="flex items-center gap-1 sm:gap-3 bg-slate-100 dark:bg-[#233648] rounded-md px-1 sm:px-2 py-0.5 sm:py-1 h-6 sm:h-7">
                                <button wire:click="updateQuantity({{ $id }}, -1)"
                                        class="text-slate-500 hover:text-primary p-0.5">
                                    <span class="material-symbols-outlined text-[14px] sm:text-[16px]">remove</span>
                                </button>
                                <span class="text-xs sm:text-sm font-bold w-3 sm:w-4 text-center dark:text-white">
                                    {{ $item['quantity'] }}
                                </span>
                                <button wire:click="updateQuantity({{ $id }}, 1)"
                                        class="text-slate-500 hover:text-primary p-0.5">
                                    <span class="material-symbols-outlined text-[14px] sm:text-[16px]">add</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center h-full opacity-30">
                    <span class="material-symbols-outlined text-4xl sm:text-6xl">shopping_cart</span>
                    <p class="mt-2 font-medium text-sm sm:text-base">cart empty</p>
                </div>
                @endforelse
            </div>

            <!-- Cart Footer -->
            <div class="p-4 sm:p-5 bg-slate-50 dark:bg-[#111a22] border-t border-slate-200 dark:border-[#233648]">
                <!-- Totals -->
                <div class="space-y-1 sm:space-y-2 mb-3 sm:mb-4 text-xs sm:text-sm">
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>Subtotal</span>
                        <span>Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>Tax (10%)</span>
                        <span>Rp{{ number_format($tax, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full border-t border-slate-200 dark:border-[#233648] my-1 sm:my-2"></div>
                    <div class="flex justify-between items-end text-slate-900 dark:text-white">
                        <span class="font-bold text-base sm:text-lg">Total</span>
                        <span class="font-bold text-lg sm:text-2xl">Rp{{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="grid grid-cols-4 gap-1 sm:gap-2 mb-3 sm:mb-4">
                    <button wire:click="$set('paymentMethod', 'cash')"
                            class="h-8 sm:h-10 rounded active:scale-[0.98] {{ $paymentMethod === 'cash' ? 'bg-primary/20 border border-primary text-primary' : 'bg-slate-200 dark:bg-[#233648] text-slate-700 dark:text-white' }} flex items-center justify-center">
                        <span class="material-symbols-outlined text-lg sm:text-[20px]">payments</span>
                    </button>
                    <button wire:click="$set('paymentMethod', 'credit_card')"
                            class="h-8 sm:h-10 rounded active:scale-[0.98] {{ $paymentMethod === 'credit_card' ? 'bg-primary/20 border border-primary text-primary' : 'bg-slate-200 dark:bg-[#233648] text-slate-700 dark:text-white' }} flex items-center justify-center">
                        <span class="material-symbols-outlined text-lg sm:text-[20px]">credit_card</span>
                    </button>
                    <button wire:click="$set('paymentMethod', 'qr_code')"
                            class="h-8 sm:h-10 rounded active:scale-[0.98] {{ $paymentMethod === 'qr_code' ? 'bg-primary/20 border border-primary text-primary' : 'bg-slate-200 dark:bg-[#233648] text-slate-700 dark:text-white' }} flex items-center justify-center">
                        <span class="material-symbols-outlined text-lg sm:text-[20px]">qr_code_2</span>
                    </button>
                    <button class="h-8 sm:h-10 rounded active:scale-[0.98] bg-slate-200 dark:bg-[#233648] text-slate-700 dark:text-white font-medium text-sm hover:bg-primary/20 hover:text-primary transition-colors flex items-center justify-center">
                        <span class="material-symbols-outlined text-lg sm:text-[20px]">more_horiz</span>
                    </button>
                </div>

                <!-- Cash Payment Input -->
                @if($paymentMethod === 'cash')
                <div class="mb-3 sm:mb-4 p-3 sm:p-4 bg-white dark:bg-[#192633] rounded-xl border border-slate-200 dark:border-[#233648] shadow-sm">
                    <div class="mb-3 sm:mb-4">
                        <label class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1 sm:mb-2 block">
                            Cash Amount
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 text-slate-500 font-medium text-xs sm:text-sm">Rp</span>
                            <input type="number" 
                                   wire:model.live="cashGiven" 
                                   min="0" 
                                   step="1000"
                                   class="w-full h-10 sm:h-14 pl-8 sm:pl-12 pr-3 sm:pr-4 rounded-xl bg-slate-100 dark:bg-[#233648] border-2 border-transparent focus:border-primary focus:bg-white dark:focus:bg-[#1e2e3f] text-right text-base sm:text-lg font-bold text-slate-900 dark:text-white placeholder:text-slate-400 transition-all"
                                   placeholder="0">
                        </div>
                    </div>

                    @if($cashGiven > 0)
                    <div class="space-y-1 sm:space-y-2 pt-2 sm:pt-3 border-t border-slate-200 dark:border-[#233648]">
                        <div class="flex justify-between items-center text-xs sm:text-sm">
                            <span class="text-slate-600 dark:text-slate-400">Total Bill</span>
                            <span class="font-semibold text-slate-900 dark:text-white">Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>

                        @if($cashGiven >= $total)
                        <div class="flex justify-between items-center bg-green-50 dark:bg-green-900/20 p-2 sm:p-3 rounded-lg">
                            <span class="text-xs sm:text-sm font-medium text-green-700 dark:text-green-400">Change</span>
                            <span class="font-bold text-sm sm:text-lg text-green-600 dark:text-green-400">
                                Rp{{ number_format($cashGiven - $total, 0, ',', '.') }}
                            </span>
                        </div>
                        @elseif($cashGiven < $total)
                        <div class="flex justify-between items-center bg-red-50 dark:bg-red-900/20 p-2 sm:p-3 rounded-lg">
                            <span class="text-xs sm:text-sm font-medium text-red-600 dark:text-red-400">Shortage</span>
                            <span class="font-bold text-sm sm:text-lg text-red-500 dark:text-red-400">
                                Rp{{ number_format($total - $cashGiven, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                @endif

                <!-- Checkout Button -->
                <button wire:click="handleCheckout" 
                        @if($paymentMethod==='cash' && $cashGiven < $total) disabled @endif
                        class="w-full h-10 sm:h-14 rounded-xl font-bold text-sm sm:text-lg flex items-center justify-center px-4 sm:px-6 transition-all active:scale-[0.98] shadow-lg 
                        {{ ($paymentMethod === 'cash' && $cashGiven < $total) ? 'bg-slate-300 dark:bg-slate-600 cursor-not-allowed text-slate-500 dark:text-slate-400' : 'bg-primary hover:bg-blue-600 text-white' }}">
                    <span class="flex items-center gap-1 sm:gap-2">
                        <span class="material-symbols-outlined text-lg sm:text-2xl">check_circle</span>
                        <span class="whitespace-nowrap">Complete Sale</span>
                    </span>
                </button>
            </div>
        </aside>
    </main>
</div>