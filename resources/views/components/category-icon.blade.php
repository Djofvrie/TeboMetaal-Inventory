@props(['slug'])
@switch($slug)
    @case('koker')
        <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" class="w-full h-full">
            <rect x="8" y="8" width="48" height="48" rx="2"/>
            <rect x="18" y="18" width="28" height="28" rx="1"/>
        </svg>
        @break
    @case('buis')
        <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" class="w-full h-full">
            <circle cx="32" cy="32" r="24"/>
            <circle cx="32" cy="32" r="14"/>
        </svg>
        @break
    @case('dikwandige-buis')
        <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" class="w-full h-full">
            <circle cx="32" cy="32" r="24"/>
            <circle cx="32" cy="32" r="10"/>
            <path d="M14 32h12M38 32h12" stroke-width="1.5" stroke-dasharray="2 2"/>
        </svg>
        @break
    @case('strip')
        <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" class="w-full h-full">
            <rect x="4" y="24" width="56" height="16" rx="1"/>
        </svg>
        @break
    @case('hoeklijn')
        <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" class="w-full h-full">
            <path d="M12 8v48h48" stroke-width="3"/>
            <path d="M20 8v40h40" stroke-width="3"/>
        </svg>
        @break
    @case('vierkant')
        {{-- Massief vierkant staafprofiel (doorsnede) --}}
        <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" class="w-full h-full">
            <rect x="12" y="12" width="40" height="40" rx="1"/>
            <line x1="12" y1="12" x2="52" y2="52" stroke-width="1.5"/>
            <line x1="52" y1="12" x2="12" y2="52" stroke-width="1.5"/>
        </svg>
        @break
    @case('plaat')
        {{-- Platte stalen plaat --}}
        <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" class="w-full h-full">
            <rect x="4" y="26" width="56" height="4" rx="0.5"/>
        </svg>
        @break
    @case('as')
        {{-- Massieve ronde staaf (doorsnede) --}}
        <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" class="w-full h-full">
            <circle cx="32" cy="32" r="22"/>
            <circle cx="32" cy="32" r="2" fill="currentColor"/>
            <line x1="32" y1="10" x2="32" y2="30" stroke-width="1.5"/>
            <line x1="32" y1="34" x2="32" y2="54" stroke-width="1.5"/>
            <line x1="10" y1="32" x2="30" y2="32" stroke-width="1.5"/>
            <line x1="34" y1="32" x2="54" y2="32" stroke-width="1.5"/>
        </svg>
        @break
    @case('hea')
        {{-- HEA breed-flens I-profiel (H-balk) --}}
        <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" class="w-full h-full">
            <path d="M12 8h40M12 56h40" stroke-width="4"/>
            <line x1="32" y1="8" x2="32" y2="56" stroke-width="4"/>
            <line x1="12" y1="8" x2="12" y2="56" stroke-width="1" stroke-dasharray="2 4"/>
            <line x1="52" y1="8" x2="52" y2="56" stroke-width="1" stroke-dasharray="2 4"/>
        </svg>
        @break
    @case('ipe')
        {{-- IPE smal-flens I-profiel --}}
        <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" class="w-full h-full">
            <path d="M16 8h32M16 56h32" stroke-width="4"/>
            <line x1="32" y1="8" x2="32" y2="56" stroke-width="3"/>
        </svg>
        @break
    @case('t-profiel')
        {{-- T-profiel --}}
        <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" class="w-full h-full">
            <path d="M8 8h48" stroke-width="4"/>
            <line x1="32" y1="8" x2="32" y2="56" stroke-width="4"/>
        </svg>
        @break
    @case('u-profiel')
        {{-- U-profiel (kanaal) --}}
        <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" class="w-full h-full">
            <path d="M16 8v48h32v-48" stroke-width="4" fill="none"/>
            <path d="M24 8v40h16v-40" stroke-width="1" stroke-dasharray="2 3"/>
        </svg>
        @break
    @default
        <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" class="w-full h-full">
            <rect x="8" y="8" width="48" height="48" rx="4"/>
            <circle cx="32" cy="32" r="8"/>
        </svg>
        @break
@endswitch
