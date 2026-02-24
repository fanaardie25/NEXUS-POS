

<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
 <title>{{ $title ?? config('app.name') }}</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
@livewireStyles
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                        "surface-dark": "#192633",
                        "surface-dark-hover": "#233648",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
<style>
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Custom scrollbar for better aesthetics in dark mode */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #101922; 
        }
        ::-webkit-scrollbar-thumb {
            background: #233648; 
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #137fec; 
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white h-screen flex flex-col font-display">


 {{ $slot }}

 @livewireScripts
 <script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCode = null;
    let scannerActive = false;
    let isScanning = false;
    const closeScan = document.getElementById('closeScanner');

    closeScan.addEventListener('click', () => {
        stopScanner().then(() => {
            document.getElementById('reader').classList.add('hidden');
            document.getElementById('scannerModal').classList.add('hidden');
        });
    });

    function startScan() {
        const reader = document.getElementById('reader');
        const modal = document.getElementById('scannerModal');

        
        if (reader.classList.contains('hidden')) {
            reader.classList.remove('hidden');
        
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
            
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
                modal.classList.add('hidden');
                modal.classList.remove('flex');
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
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });
        };

        const config = { 
            fps: 20, 
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
<script>
        // Simple JS just to update the time, as per modern dashboard expectations
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const dateString = now.toLocaleDateString([], { month: 'short', day: 'numeric' });
            document.getElementById('current-time').textContent = `${dateString}, ${timeString}`;
        }
        updateTime();
        setInterval(updateTime, 60000); // Update every minute
    </script>
    
</body></html>
