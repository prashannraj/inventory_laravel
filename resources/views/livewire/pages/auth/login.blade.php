<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-8">
        <h2 class="text-2xl font-black text-gray-900 tracking-tighter uppercase">Welcome Back</h2>
        <p class="text-sm text-gray-500 font-medium">Please enter your credentials to access the system.</p>
    </div>

    <form wire:submit="login" class="space-y-6">
        <!-- Login (Email or Username) -->
        <div class="relative group">
            <x-input-label for="login" :value="__('Email or Username')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1 ml-1" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-600 transition-colors">
                    <i class="fas fa-user-circle"></i>
                </div>
                <x-text-input wire:model="form.login" id="login" class="block w-full pl-11 bg-gray-50 border-gray-200 focus:bg-white focus:ring-indigo-500 focus:border-indigo-500 rounded-xl transition-all" type="text" name="login" required autofocus autocomplete="username" placeholder="admin@example.com" />
            </div>
            <x-input-error :messages="$errors->get('form.login')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="relative group">
            <div class="flex justify-between items-center mb-1 ml-1">
                <x-input-label for="password" :value="__('Password')" class="text-[10px] font-black uppercase tracking-widest text-gray-400" />
                @if (Route::has('password.request'))
                    <a class="text-[10px] font-black uppercase tracking-widest text-indigo-600 hover:text-indigo-800 transition-colors" href="{{ route('password.request') }}" wire:navigate>
                        {{ __('Forgot?') }}
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-600 transition-colors">
                    <i class="fas fa-lock"></i>
                </div>
                <x-text-input wire:model="form.password" id="password" class="block w-full pl-11 bg-gray-50 border-gray-200 focus:bg-white focus:ring-indigo-500 focus:border-indigo-500 rounded-xl transition-all"
                                type="password"
                                name="password"
                                required autocomplete="current-password"
                                placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember" class="inline-flex items-center group cursor-pointer">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded-md border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-5 h-5 transition-all" name="remember">
                <span class="ms-3 text-sm font-bold text-gray-500 group-hover:text-gray-700 transition-colors">{{ __('Stay logged in') }}</span>
            </label>
        </div>

        <div>
            <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase tracking-widest rounded-xl shadow-[0_10px_20px_rgba(79,70,229,0.3)] hover:shadow-[0_15px_25px_rgba(79,70,229,0.4)] transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                Sign In to Dashboard
            </button>
        </div>
    </form>
</div>
