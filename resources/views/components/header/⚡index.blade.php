<?php

use Livewire\Component;

new class extends Component
{
        public $user;
        public $role;
        public function mount()
        {
            $this->user = auth()->user();
            $this->role = auth()->user()->getRoleNames()->first();
        }
    
        public function render()
        {
            return $this->view([
                'user' => $this->user
            ]);
        }
};
?>

<div>
  <header class="flex-none flex items-center justify-between whitespace-nowrap border-b border-slate-200 dark:border-[#233648] px-6 py-3 bg-white dark:bg-[#111a22]">
    
    <!-- Left Section -->
    <div class="flex items-center gap-4">
        <h2 class="text-slate-900 dark:text-white text-xl font-bold tracking-tight">
            Nexus POS
        </h2>
    </div>

    <!-- Right Section -->
    <div class="flex items-center gap-6">
        
        <!-- Time -->
        <div class="hidden md:flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
            <span class="material-symbols-outlined text-xl">schedule</span>
            <span id="current-time">{{ Now() }}</span>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3">

            <button class="flex items-center justify-center size-10 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-[#233648] dark:hover:bg-[#2d465e] text-slate-700 dark:text-white transition-colors">
                <a href="/admin" class="material-symbols-outlined" >settings</a>
            </button>

            <div class="h-10 w-px bg-slate-200 dark:bg-[#233648] mx-1"></div>

            <!-- User -->
            <button class="flex items-center gap-3">
                <div
                    class="size-10 rounded-full bg-center bg-cover ring-2 ring-slate-100 dark:ring-[#233648]"
                    data-alt="User profile picture"
                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDbnuw43Ptl2zeWhT2z6Lg7b_xyhIvZVDa086ddDdGabCbT0q1rTX0NlnpeEsrSF-FikeaQhX733Kf1P7ff4a3eVW4i2047wnJZRXkFvakjSr4Uot4Y7HrTGP9vefQf8fUVoDQUQ7GJaKmSkxzubIpOU-eVkS1Ho9rUydqL0DcOUjS64F8EU0xvi1ynJK9j9AV3Nrf9samZv2h3Te7PMFZSH2QtziYiDFPlqzIhL93Ckw78zirr3FeG4DsgjcVsaX6bZ0nTIEqjBdg");'
                ></div>

                <div class="hidden lg:flex flex-col items-start">
                    <span class="text-sm font-semibold text-slate-900 dark:text-white">
                        {{ $user->name }}
                    </span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $role }} #{{ $user->id }}
                    </span>
                </div>
            </button>
        </div>
    </div>
</header>
</div>