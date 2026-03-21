{{-- Breadcrumbs component
     Usage: @include('components.breadcrumbs', ['items' => [['label' => 'Inicio', 'url' => route('home')], ['label' => 'Adeslas']]])
     The last item should NOT have a 'url' key (it's the current page).
--}}
@if(!empty($items))
<nav aria-label="Ruta de navegación">
    <ol class="flex flex-wrap items-center gap-1.5 text-sm text-gray-500" itemscope itemtype="https://schema.org/BreadcrumbList">
        @foreach($items as $i => $item)
            <li class="flex items-center gap-1.5" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                @if(isset($item['url']))
                    <a href="{{ $item['url'] }}" itemprop="item" class="hover:text-primary transition-colors">
                        @if($i === 0)
                            <i class="fa-solid fa-house text-xs mr-0.5"></i>
                        @endif
                        <span itemprop="name">{{ $item['label'] }}</span>
                    </a>
                @else
                    <span itemprop="item" content="{{ url()->current() }}">
                        <span itemprop="name" class="text-ink font-medium">{{ $item['label'] }}</span>
                    </span>
                @endif
                <meta itemprop="position" content="{{ $i + 1 }}">
                @if(!$loop->last)
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif
