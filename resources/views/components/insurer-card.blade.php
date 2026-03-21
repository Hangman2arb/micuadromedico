{{-- Insurer Card component
     Usage: @include('components.insurer-card', ['insurer' => $insurer])
     Expects $insurer with: name, slug, brand_color (hex), logo (nullable), provinces_count
--}}
@php
    $color = $insurer->brand_color ?? '#1d5fa7';
    $initial = mb_strtoupper(mb_substr($insurer->name, 0, 1));
    $provinceCount = $insurer->provinces_count ?? 0;
    $logoPath = 'images/logos/' . $insurer->slug . '.webp';
    $hasLogo = file_exists(public_path($logoPath));
@endphp

<a href="{{ route('insurer.show', $insurer->slug) }}"
   class="group block bg-white rounded-xl border border-gray-200 p-5 hover:border-transparent hover:shadow-lg hover:shadow-gray-200/50 transition-all duration-300 relative overflow-hidden"
   style="--card-color: {{ $color }}"
>
    {{-- Color accent bar --}}
    <div class="absolute top-0 left-0 right-0 h-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300" style="background-color: {{ $color }}"></div>

    <div class="flex items-center gap-4">
        {{-- Logo / Initial --}}
        <div class="shrink-0">
            @if($hasLogo)
                <img src="{{ asset($logoPath) }}"
                     alt="Logo {{ $insurer->name }}"
                     class="w-14 h-14 rounded-xl object-contain border border-gray-100 p-1.5 bg-white"
                     loading="lazy"
                     width="56" height="56">
            @elseif($insurer->logo ?? false)
                <img src="{{ asset($insurer->logo) }}"
                     alt="Logo {{ $insurer->name }}"
                     class="w-14 h-14 rounded-xl object-contain border border-gray-100 p-1.5"
                     loading="lazy"
                     width="56" height="56">
            @else
                <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-sm"
                     style="background-color: {{ $color }}">
                    {{ $initial }}
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="flex-1 min-w-0">
            <h3 class="font-bold text-ink text-[15px] leading-tight group-hover:text-primary transition-colors truncate">
                {{ $insurer->name }}
            </h3>
            @if($provinceCount > 0)
                <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-map-location-dot"></i>
                    {{ $provinceCount }} {{ $provinceCount === 1 ? 'provincia' : 'provincias' }}
                </p>
            @endif
        </div>

        {{-- Arrow --}}
        <div class="shrink-0 w-8 h-8 rounded-full bg-gray-50 group-hover:bg-primary/10 flex items-center justify-center transition-colors">
            <i class="fa-solid fa-arrow-right text-xs text-gray-300 group-hover:text-primary transition-colors"></i>
        </div>
    </div>
</a>
