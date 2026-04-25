@props([
    'title',
    'value',
    'icon',
    'color' => 'indigo',
    'trend' => null,
    'label' => '',
    'link' => '#',
    'linkText' => 'View Details'
])

@php
    $bgColors = [
        'indigo' => 'bg-indigo-50',
        'blue' => 'bg-blue-50',
        'emerald' => 'bg-emerald-50',
        'rose' => 'bg-rose-50',
        'amber' => 'bg-amber-50',
        'violet' => 'bg-violet-50',
    ];
    $iconColors = [
        'indigo' => 'text-indigo-600',
        'blue' => 'text-blue-600',
        'emerald' => 'text-emerald-600',
        'rose' => 'text-rose-600',
        'amber' => 'text-amber-600',
        'violet' => 'text-violet-600',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 transition-all hover:shadow-md group']) }}>
    <div class="flex items-center justify-between mb-3 sm:mb-4">
        <div class="p-2 sm:p-3 {{ $bgColors[$color] ?? 'bg-gray-50' }} rounded-lg sm:rounded-xl transition-transform group-hover:scale-110">
            <i class="{{ $icon }} {{ $iconColors[$color] ?? 'text-gray-600' }} text-lg sm:text-xl"></i>
        </div>
        @if($trend)
            <span class="text-xs font-bold {{ $trend > 0 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} px-2 py-1 rounded-full whitespace-nowrap">
                {{ $trend > 0 ? '+' : '' }}{{ $trend }}%
            </span>
        @endif
    </div>
    
    <h3 class="text-xs sm:text-sm font-semibold text-gray-500 uppercase tracking-wider">{{ $title }}</h3>
    
    <div class="flex items-baseline gap-2 mt-1">
        <p class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">{{ $value }}</p>
        @if($label)
            <p class="text-xs text-gray-400 font-bold uppercase tracking-tighter hidden xs:inline">{{ $label }}</p>
        @endif
    </div>
    
    <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-50">
        <a href="{{ $link }}" class="text-[10px] font-black {{ $iconColors[$color] ?? 'text-indigo-600' }} hover:opacity-80 flex items-center uppercase tracking-widest transition-all">
            <span class="truncate">{{ $linkText }}</span> <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform flex-shrink-0"></i>
        </a>
    </div>
</div>
