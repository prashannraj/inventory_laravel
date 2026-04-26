<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div x-data="{ sidebarOpen: false }">
    <!-- Top Header -->
    <header class="fixed top-0 right-0 left-0 sm:left-64 h-16 bg-white border-b border-gray-100 z-30 transition-all duration-300">
        <div class="h-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <!-- Left: Mobile Toggle -->
            <div class="flex items-center">
                <button @click="sidebarOpen = !sidebarOpen" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none sm:hidden transition-colors">
                    <i class="fas fa-bars text-xl" x-show="!sidebarOpen"></i>
                    <i class="fas fa-times text-xl" x-show="sidebarOpen"></i>
                </button>
                
                <!-- Search Placeholder -->
                <div class="hidden md:flex relative ml-4">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input type="text" placeholder="Search data..." class="pl-10 block w-64 bg-gray-50 border-none rounded-xl text-xs focus:ring-2 focus:ring-indigo-500/20 transition-all">
                </div>
            </div>

            <!-- Right: User Actions -->
            <div class="flex items-center gap-2 sm:gap-4">
                <!-- Notifications -->
                <button class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all relative">
                    <i class="far fa-bell text-lg"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full border-2 border-white"></span>
                </button>

                <!-- Profile Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 p-1 rounded-xl hover:bg-gray-50 transition-all border border-transparent">
                            <div class="hidden sm:block text-right">
                                <p class="text-xs font-black text-gray-900 leading-none uppercase tracking-tighter">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">{{ auth()->user()->roles->first()?->name ?? 'Staff' }}</p>
                            </div>
                            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                                <i class="fas fa-user-tie"></i>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-50">
                            <p class="text-xs text-gray-500 font-medium">Logged in as</p>
                            <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            <i class="fas fa-user-circle mr-2 opacity-50"></i> {{ __('My Profile') }}
                        </x-dropdown-link>
                        @can('updateSetting')
                        <x-dropdown-link :href="route('company.edit')" wire:navigate>
                            <i class="fas fa-cog mr-2 opacity-50"></i> {{ __('Settings') }}
                        </x-dropdown-link>
                        @endcan
                        <div class="border-t border-gray-50"></div>
                        <button wire:click="logout" class="w-full text-left">
                            <x-dropdown-link class="text-rose-600 font-bold">
                                <i class="fas fa-sign-out-alt mr-2 opacity-50"></i> {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </header>

    <!-- Sidebar Overlay (Mobile) -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 sm:hidden transition-opacity"></div>

    <!-- Sidebar Container -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0'" class="fixed top-0 left-0 bottom-0 w-64 bg-white border-r border-gray-100 z-50 transition-transform duration-300 ease-in-out shadow-xl sm:shadow-none">
        
        <!-- Logo Area -->
        <div class="h-16 flex items-center px-6 border-b border-gray-50">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 group">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <i class="fas fa-cube"></i>
                </div>
                <span class="font-black text-lg text-gray-900 tracking-tighter uppercase truncate">{{ $siteSettings['company_name'] ?? 'Inventory' }}</span>
            </a>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-6 overflow-y-auto space-y-8 h-[calc(100vh-4rem)]">
            
            <!-- Dashboard Section -->
            <div>
                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                    <i class="fas fa-grid-2 mr-3 text-lg"></i> Dashboard
                </x-sidebar-link>
            </div>

            <!-- Master Data -->
            <div>
                <h4 class="px-3 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Master Data</h4>
                <div class="space-y-1">
                    @can('viewUser')
                    <x-sidebar-link :href="route('users.index')" :active="request()->routeIs('users.*')" wire:navigate>
                        <i class="fas fa-users-gear mr-3"></i> Team
                    </x-sidebar-link>
                    @endcan

                    @can('viewSupplier')
                    <x-sidebar-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" wire:navigate>
                        <i class="fas fa-truck-fast mr-3"></i> Suppliers
                    </x-sidebar-link>
                    @endcan

                    @can('viewCustomer')
                    <x-sidebar-link :href="route('customers.index')" :active="request()->routeIs('customers.*')" wire:navigate>
                        <i class="fas fa-user-group mr-3"></i> Customers
                    </x-sidebar-link>
                    @endcan
                    
                    @can('viewBrand')
                    <x-sidebar-link :href="route('brands.index')" :active="request()->routeIs('brands.*')" wire:navigate>
                        <i class="fas fa-tags mr-3"></i> Brands
                    </x-sidebar-link>
                    @endcan
                    
                    @can('viewCategory')
                    <x-sidebar-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" wire:navigate>
                        <i class="fas fa-layer-group mr-3"></i> Categories
                    </x-sidebar-link>
                    @endcan

                    <x-sidebar-link :href="route('payment-methods.index')" :active="request()->routeIs('payment-methods.*')" wire:navigate>
                        <i class="fas fa-credit-card mr-3"></i> Payment Methods
                    </x-sidebar-link>

                    @can('viewStore')
                    <x-sidebar-link :href="route('stores.index')" :active="request()->routeIs('stores.*')" wire:navigate>
                        <i class="fas fa-store mr-3"></i> Stores
                    </x-sidebar-link>
                    @endcan

                    @can('viewTaxRate')
                    <x-sidebar-link :href="route('tax-rates.index')" :active="request()->routeIs('tax-rates.*')" wire:navigate>
                        <i class="fas fa-percent mr-3"></i> Tax Rates
                    </x-sidebar-link>
                    @endcan

                    @can('viewUnit')
                    <x-sidebar-link :href="route('units.index')" :active="request()->routeIs('units.*')" wire:navigate>
                        <i class="fas fa-ruler mr-3"></i> Units
                    </x-sidebar-link>
                    @endcan

                    <x-sidebar-link :href="route('invoice-templates.index')" :active="request()->routeIs('invoice-templates.*')" wire:navigate>
                        <i class="fas fa-file-invoice mr-3"></i> Invoice Templates
                    </x-sidebar-link>
                </div>
            </div>

            <!-- Inventory -->
            <div>
                <h4 class="px-3 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Inventory</h4>
                <div class="space-y-1">
                    @can('viewProduct')
                    <x-sidebar-link :href="route('products.index')" :active="request()->routeIs('products.*')" wire:navigate>
                        <i class="fas fa-box-archive mr-3"></i> Products
                    </x-sidebar-link>
                    @endcan

                    @can('viewPurchase')
                    <x-sidebar-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')" wire:navigate>
                        <i class="fas fa-file-import mr-3"></i> Purchases
                    </x-sidebar-link>
                    @endcan
                    
                    <x-sidebar-link :href="route('orders.index')" :active="request()->routeIs('orders.*')" wire:navigate>
                        <i class="fas fa-shopping-cart mr-3"></i> Orders
                    </x-sidebar-link>

                    @can('viewSale')
                    <x-sidebar-link :href="route('sales.index')" :active="request()->routeIs('sales.*')" wire:navigate>
                        <i class="fas fa-receipt mr-3"></i> Sales History
                    </x-sidebar-link>
                    @endcan

                    @can('viewAdjustment')
                    <x-sidebar-link :href="route('stock-adjustments.index')" :active="request()->routeIs('stock-adjustments.*')" wire:navigate>
                        <i class="fas fa-sliders mr-3"></i> Adjustments
                    </x-sidebar-link>
                    @endcan
                </div>
            </div>

            <!-- Intelligence -->
            <div>
                <h4 class="px-3 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Intelligence</h4>
                <div class="space-y-1">
                    @can('viewReports')
                    <x-sidebar-link :href="route('reports.index')" :active="request()->routeIs('reports.*')" wire:navigate>
                        <i class="fas fa-chart-pie mr-3"></i> Reports
                    </x-sidebar-link>
                    @endcan
                </div>
            </div>

            <!-- Footer Area inside Sidebar -->
            <div class="pt-6 mt-6 border-t border-gray-50">
                <div class="p-4 bg-gray-50 rounded-2xl">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Help Center</p>
                    <p class="text-xs text-gray-600 mb-4">Need help managing your inventory?</p>
                    <a href="https://appantech.com.np" target="_blank" class="block w-full py-2 bg-white border border-gray-200 text-center text-xs font-bold text-indigo-600 rounded-xl hover:shadow-sm transition-all">Get Support</a>
                    <div class="mt-4 flex items-center justify-center gap-2 text-indigo-600">
                        <i class="fas fa-phone-alt text-[10px]"></i>
                        <a href="tel:+9779807669230" class="text-[10px] font-black tracking-widest">+9779807669230</a>
                    </div>
                </div>
            </div>
        </nav>
    </aside>
</div>
