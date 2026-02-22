<div>
    <livewire:header.index />
    <div x-data="{ show: false, message: '', type: 'success' }" x-init="$wire.on('alert', (data) => { 
         show = true; 
         message = data.message; 
         type = data.type; 
         setTimeout(() => show = false, 2500) 
     })" x-show="show" x-transition class="fixed top-5 right-5 z-50" style="display: none;">

        <div class="px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 min-w-[250px]" :class="{
             'bg-green-500': type === 'success',
             'bg-red-500': type === 'error',
             'bg-yellow-500': type === 'warning',
             'bg-blue-500': type === 'info'
         }">

            <span class="material-symbols-outlined text-white text-sm">
                <template x-if="type === 'success'">check</template>
                <template x-if="type === 'error'">close</template>
                <template x-if="type === 'warning'">warning</template>
                <template x-if="type === 'info'">info</template>
            </span>

            <span class="text-white text-sm font-medium" x-text="message"></span>
        </div>
    </div>


    @if (session()->has('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2500)" x-show="show" x-transition
        class="fixed top-5 right-5 z-50 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 min-w-[250px]">
        <span class="material-symbols-outlined text-white text-sm">check</span>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if (session()->has('error'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2500)" x-show="show" x-transition
        class="fixed top-5 right-5 z-50 bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 min-w-[250px]">
        <span class="material-symbols-outlined text-white text-sm">close</span>
        <span class="text-sm font-medium">{{ session('error') }}</span>
    </div>
    @endif
    <main class="flex flex-1 overflow-hidden h-[calc(100vh-64px)]">
        <section
            class="flex flex-col flex-1 min-w-0 bg-slate-50 dark:bg-[#111a22] border-r border-slate-200 dark:border-[#233648]">
            <div class="sticky top-0 z-10 bg-white/80 dark:bg-[#111a22]/95 backdrop-blur-md border-b p-4 space-y-4">
                <div class="relative w-full flex gap-2">
                    <div class="relative flex-1 ">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 ">
                            <span class="material-symbols-outlined">search</span>
                        </div>
                        <input wire:model.live.debounce.200ms="search" id="search-input" type="text"
                            placeholder="Search products..."
                            class="w-full h-12 pl-12 pr-4 rounded-xl bg-slate-100 dark:bg-[#233648] border-0 focus:ring-2 focus:ring-primary" />
                    </div>

                    <button onclick="startScan()"
                        class="p-3 bg-primary text-white rounded-xl hover:bg-blue-600 transition-all active:scale-[0.98] ">
                        <span class="material-symbols-outlined">qr_code_scanner</span>
                    </button>
                </div>

                <div id="reader" style="width: 100%" class="hidden mt-4 rounded-xl overflow-hidden"></div>

                <div class="flex gap-3 overflow-x-auto no-scrollbar pb-1">
                    <button wire:click="$set('activeCategory', 'all')"
                        class="flex items-center px-5 h-9 rounded-full {{ $activeCategory == 'all' ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-[#233648] text-slate-600' }} text-sm font-semibold transition-all">
                        All Items
                    </button>
                    @foreach ($categories as $item)
                    <button wire:click="$set('activeCategory', {{ $item->id }})"
                        class="flex items-center px-5 h-9 rounded-full active:scale-[0.98]  {{ $activeCategory == $item->id ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-[#233648] text-slate-600' }} text-sm font-semibold transition-all">
                        {{ $item->name }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 md:p-6">
                <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @if ($products->count() == 0)
                    <div class="col-span-full text-center py-10">
                        <p class="text-slate-500 dark:text-slate-400">No products found for matching "{{ $search }}".
                        </p>
                    </div>
                    @endif
                    @foreach ($products as $product)
                    <div wire:click="addToCart({{ $product->id }})" wire:key="prod-{{ $product->id }}"
                        class="group relative flex active:scale-[0.98] flex-col bg-white dark:bg-[#192633] rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all border cursor-pointer 
            {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed pointer-events-none border-red-300 dark:border-red-800' : '' }}">

                        <div class="aspect-[4/3] w-full bg-slate-200 relative overflow-hidden">
                            <div class="absolute inset-0 bg-cover bg-center transition-transform group-hover:scale-110
                    {{ $product->stock <= 0 ? 'grayscale' : '' }}"
                                style="background-image: url('{{ asset('storage/'.$product->image_path) }}');">
                            </div>

                            <!-- Stock Badge -->
                            <div
                                class="absolute top-2 right-2 {{ $product->stock <= 0 ? 'bg-red-500' : 'bg-black/60' }} text-white text-xs font-bold px-2 py-1 rounded-md">
                                @if($product->stock <= 0) <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">block</span> Out of Stock
                                    </span>
                                    @else
                                    {{ $product->stock }} left
                                    @endif
                            </div>


                            @if($product->stock <= 0) <div
                                class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                <span
                                    class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold rotate-[-15deg] shadow-lg">
                                    SOLD OUT
                                </span>
                        </div>
                        @endif
                    </div>

                    <div class="p-3">
                        <h3 class="font-bold text-slate-900 dark:text-white leading-tight line-clamp-1">
                            {{ $product->name }}
                        </h3>
                        <div class="flex items-center justify-between mt-1">
                            <span
                                class="text-primary font-bold {{ $product->stock <= 0 ? 'text-red-400 dark:text-red-500' : '' }}">
                                Rp{{ number_format($product->price) }}
                            </span>


                            <div
                                class="size-8 rounded-lg {{ $product->stock <= 0 ? 'bg-slate-300 dark:bg-slate-600 text-slate-500' : 'bg-slate-100 dark:bg-[#233648] text-primary group-hover:bg-primary group-hover:text-white' }} flex items-center justify-center">
                                <span class="material-symbols-outlined text-sm">
                                    {{ $product->stock <= 0 ? 'block' : 'add' }} </span>
                            </div>
                        </div>


                        @if($product->stock <= 0) <div class="mt-2 text-xs text-red-500 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">info</span>
                            Tidak tersedia
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
</div>
</div>
</section>

<aside
    class="flex flex-col w-[400px] flex-none bg-white dark:bg-[#192633] border-l border-slate-200 dark:border-[#233648] shadow-xl z-20">
    <div class="p-4 border-b border-slate-200 dark:border-[#233648] ">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-semibold text-slate-500 uppercase">Current Order</h3>
            <button wire:click="clearAll" class="text-primary hover:text-blue-400 text-sm font-medium">Clear
                All</button>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-4 space-y-3">
        @forelse($cart as $id => $item)
        <div class="flex gap-3 items-start group" wire:key="cart-{{ $id }}">
            <div class="size-16 rounded-lg bg-slate-100 bg-cover bg-center flex-none"
                style="background-image: url({{ asset('storage/'.$item['image_path']) }});">
            </div>
            <div class="flex-1 flex flex-col justify-between h-16">
                <div class="flex justify-between items-start">
                    <h4 class="font-medium text-slate-900 dark:text-white leading-tight line-clamp-1">{{
                        $item['name'] }}</h4>
                    <span class="font-bold text-slate-900 dark:text-white ml-2">Rp{{
                        number_format($item['price'] * $item['quantity']) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="text-xs text-slate-500">Rp{{ number_format($item['price']) }} / unit</div>
                    <div class="flex items-center gap-3 bg-slate-100 dark:bg-[#233648] rounded-md px-2 py-1 h-7">
                        <button wire:click="updateQuantity({{ $id }}, -1)"
                            class="text-slate-500 hover:text-primary"><span
                                class="material-symbols-outlined text-[16px]">remove</span></button>
                        <span class="text-sm font-bold w-4 text-center dark:text-white">{{ $item['quantity']
                            }}</span>
                        <button wire:click="updateQuantity({{ $id }}, 1)"
                            class="text-slate-500 hover:text-primary"><span
                                class="material-symbols-outlined text-[16px]">add</span></button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center h-full opacity-30">
            <span class="material-symbols-outlined text-6xl">shopping_cart</span>
            <p class="mt-2 font-medium">cart empty</p>
        </div>
        @endforelse
    </div>

    <div class="p-5 bg-slate-50 dark:bg-[#111a22] border-t border-slate-200 dark:border-[#233648]">
        <div class="space-y-2 mb-4 text-sm">
            <div class="flex justify-between text-slate-600 dark:text-slate-400">
                <span>Subtotal</span>
                <span>Rp{{ number_format($subtotal) }}</span>
            </div>
            <div class="flex justify-between text-slate-600 dark:text-slate-400">
                <span>Tax (10%)</span>
                <span>Rp{{ number_format($tax) }}</span>
            </div>
            <div class="w-full border-t border-slate-200 dark:border-[#233648] my-2"></div>
            <div class="flex justify-between items-end text-slate-900 dark:text-white">
                <span class="font-bold text-lg">Total</span>
                <span class="font-bold text-2xl">Rp{{ number_format($total) }}</span>
            </div>
        </div>

        <!-- Payment Methods Quick Select -->
        <div class="grid grid-cols-4 gap-2 mb-4">
            <button wire:click="$set('paymentMethod', 'cash')"
                class="h-10 rounded active:scale-[0.98]  {{ $paymentMethod === 'cash' ? 'bg-primary/20 border border-primary text-primary' : 'bg-slate-200 dark:bg-[#233648] text-slate-700 dark:text-white' }} ">
                <span class="material-symbols-outlined text-[20px]">payments</span>
            </button>
            <button wire:click="$set('paymentMethod', 'credit_card')"
                class="h-10 rounded active:scale-[0.98]  {{ $paymentMethod === 'credit_card' ? 'bg-primary/20 border border-primary text-primary' : 'bg-slate-200 dark:bg-[#233648] text-slate-700 dark:text-white' }}">
                <span class="material-symbols-outlined text-[20px]">credit_card</span>
            </button>
            <button wire:click="$set('paymentMethod', 'qr_code')"
                class="h-10 rounded active:scale-[0.98]  {{ $paymentMethod === 'qr_code' ? 'bg-primary/20 border border-primary text-primary' : 'bg-slate-200 dark:bg-[#233648] text-slate-700 dark:text-white' }} font-medium text-sm hover:bg-primary/20 hover:text-primary transition-colors flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">qr_code_2</span>
            </button>
            <button
                class="h-10 rounded active:scale-[0.98]  bg-slate-200 dark:bg-[#233648] text-slate-700 dark:text-white font-medium text-sm hover:bg-primary/20 hover:text-primary transition-colors flex items-center justify-center">
                ...
            </button>
        </div>

        @if($paymentMethod === 'cash')
        <div
            class="mb-4 p-4 bg-white dark:bg-[#192633] rounded-xl border border-slate-200 dark:border-[#233648] shadow-sm">
            <div class="mb-4">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 block">
                    Cash Amount
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-medium">Rp</span>
                    <input type="number" wire:model.live="cashGiven" min="0" step="1000"
                        class="w-full h-14 pl-12 pr-4 rounded-xl bg-slate-100 dark:bg-[#233648] border-2 border-transparent focus:border-primary focus:bg-white dark:focus:bg-[#1e2e3f] text-right text-lg font-bold text-slate-900 dark:text-white placeholder:text-slate-400 transition-all"
                        placeholder="0">
                </div>
                <p class="text-xs text-slate-500 mt-2 ml-1">
                    <span class="material-symbols-outlined text-[14px] align-text-bottom">info</span>
                    Enter the amount of cash received from customer
                </p>
            </div>

            @if($cashGiven > 0)
            <div class="space-y-2 pt-3 border-t border-slate-200 dark:border-[#233648]">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-600 dark:text-slate-400">Total Bill</span>
                    <span class="font-semibold text-slate-900 dark:text-white">Rp{{ number_format($total)
                        }}</span>
                </div>

                @if($cashGiven >= $total)
                <div class="flex justify-between items-center bg-green-50 dark:bg-green-900/20 p-3 rounded-lg">
                    <span class="text-sm font-medium text-green-700 dark:text-green-400">Change</span>
                    <span class="font-bold text-lg text-green-600 dark:text-green-400">
                        Rp{{ number_format($cashGiven - $total) }}
                    </span>
                </div>
                @elseif($cashGiven < $total) <div
                    class="flex justify-between items-center bg-red-50 dark:bg-red-900/20 p-3 rounded-lg">
                    <span class="text-sm font-medium text-red-600 dark:text-red-400">Shortage</span>
                    <span class="font-bold text-lg text-red-500 dark:text-red-400">
                        Rp{{ number_format($total - $cashGiven) }}
                    </span>
            </div>
            @endif
        </div>
        @endif
    </div>
    @endif

    <!-- Complete Sale Button - Dinamis berdasarkan payment method -->
    <button wire:click="handleCheckout" @if($paymentMethod==='cash' && $cashGiven < $total) disabled @endif
        class="w-full h-14 rounded-xl font-bold text-lg flex items-center justify-center px-6 transition-all active:scale-[0.98] shadow-lg {{ ($paymentMethod === 'cash' && $cashGiven < $total) ? 'bg-slate-300 dark:bg-slate-600 cursor-not-allowed text-slate-500 dark:text-slate-400' : 'bg-primary hover:bg-blue-600 text-white' }}">
        <span class="flex items-center gap-2">
            <span class="material-symbols-outlined">check_circle</span>
            Complete Sale
        </span>
    </button>
    </div>
</aside>
</main>
</div>


<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCode = null;
    let scannerActive = false;
    let isScanning = false;

    function startScan() {
        const reader = document.getElementById('reader');
        
        if (reader.classList.contains('hidden')) {
            reader.classList.remove('hidden');
            
            // Reset scanner state
            if (html5QrCode) {
                stopScanner().then(() => {
                    setTimeout(() => {
                        startScanner();
                    }, 100);
                });
            } else {
                setTimeout(() => {
                    startScanner();
                }, 100);
            }
        } else {
            stopScanner().then(() => {
                reader.classList.add('hidden');
            });
        }
    }

    function startScanner() {
        
        if (isScanning) {
            console.log('Scanner already running');
            return;
        }
        
        const reader = document.getElementById('reader');
        
       
        reader.innerHTML = '';
        
     
        html5QrCode = new Html5Qrcode("reader");
        isScanning = true;
        
        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            console.log("QR Code scanned:", decodedText);   
            
            
            const component = Livewire.first();

            if (component) {
                // Extract product ID from URL if needed
                const productId = extractProductId(decodedText);
                const searchValue = productId || decodedText;
                
                component.set('search', searchValue);
                
                setTimeout(() => {
                    component.call('scanProduct', searchValue);
                }, 100);
                
                showNotification('QR scan success', 'success');
            } else {
                showNotification('Component not found', 'error');
            }
            
            // Stop scanner after successful scan
            stopScanner().then(() => {
                reader.classList.add('hidden');
            });
        };

        const config = { 
            fps: 30, 
            qrbox: { width: 250, height: 250 } 
        };
        
        html5QrCode.start(
            { facingMode: "environment" }, 
            config, 
            qrCodeSuccessCallback
        ).catch((err) => {
            console.error("Error starting scanner:", err);
            showNotification("Gagal mengakses kamera. Pastikan izin kamera diberikan.", 'error');
            reader.classList.add('hidden');
            isScanning = false;
            html5QrCode = null;
        });
    }

   
    function extractProductId(url) {
        try {
            if (url.startsWith('http')) {
                const parts = url.split('/');
                const lastPart = parts[parts.length - 1];
                const cleanId = lastPart.split('?')[0].split('#')[0];
                return cleanId || null;
            }
            return null;
        } catch (e) {
            console.error('Error extracting ID:', e);
            return null;
        }
    }

    function stopScanner() {
        return new Promise((resolve) => {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    console.log("Scanner stopped");
                    html5QrCode = null;
                    isScanning = false;
                    resolve();
                }).catch((err) => {
                    console.error("Error stopping scanner:", err);
                    html5QrCode = null;
                    isScanning = false;
                    resolve();
                });
            } else {
                html5QrCode = null;
                isScanning = false;
                resolve();
            }
        });
    }

    function showNotification(message, type = 'success') {
        const existingNotif = document.getElementById('scan-notification');
        if (existingNotif) {
            existingNotif.remove();
        }
        
        const notification = document.createElement('div');
        notification.id = 'scan-notification';
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.padding = '12px 24px';
        notification.style.borderRadius = '8px';
        notification.style.color = 'white';
        notification.style.fontWeight = 'bold';
        notification.style.zIndex = '9999';
        notification.style.transition = 'all 0.3s ease';
        notification.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
        notification.style.backgroundColor = type === 'success' ? '#10b981' : '#ef4444';
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 3000);
    }

    window.addEventListener('beforeunload', function() {
        if (html5QrCode) {
            html5QrCode.stop().catch(() => {});
        }
    });

    if (typeof Livewire !== 'undefined') {
        console.log('Livewire components:', Livewire.all());
    }
</script>