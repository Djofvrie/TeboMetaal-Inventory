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
        {{-- HEA breed-flens H-profiel: brede flenzen, korte lijf --}}
        <svg viewBox="0 0 64 64" fill="currentColor" class="w-full h-full">
            <rect x="8" y="6" width="48" height="8"/>
            <rect x="8" y="50" width="48" height="8"/>
            <rect x="26" y="14" width="12" height="36"/>
        </svg>
        @break
    @case('ipe')
        {{-- IPE smal-flens I-profiel: smallere flenzen, hoger lijf --}}
        <svg viewBox="0 0 64 64" fill="currentColor" class="w-full h-full">
            <rect x="14" y="6" width="36" height="6"/>
            <rect x="14" y="52" width="36" height="6"/>
            <rect x="27" y="12" width="10" height="40"/>
        </svg>
        @break
    @case('t-profiel')
        {{-- T-profiel: flens bovenaan, lijf naar beneden --}}
        <svg viewBox="0 0 64 64" fill="currentColor" class="w-full h-full">
            <rect x="8" y="8" width="48" height="8"/>
            <rect x="26" y="16" width="12" height="40"/>
        </svg>
        @break
    @case('u-profiel')
        {{-- U-profiel: open kanaal naar boven --}}
        <svg viewBox="0 0 64 64" fill="currentColor" class="w-full h-full">
            <rect x="8" y="8" width="12" height="48"/>
            <rect x="44" y="8" width="12" height="48"/>
            <rect x="8" y="48" width="48" height="8"/>
        </svg>
        @break
    @default
        <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" class="w-full h-full">
            <rect x="8" y="8" width="48" height="48" rx="4"/>
            <circle cx="32" cy="32" r="8"/>
        </svg>
        @break
@endswitch
