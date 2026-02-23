<li>
    <a href="{{ $item['url'] }}" target="{{ $item['target'] ?? '_self' }}">
        {{ $item['label'] }}
    </a>
    
    @if(!empty($item['items']))
        <ul class="dl-submenu">
            @foreach($item['items'] as $subItem)
                @include('responsive-menu::partials.menu-item', ['item' => $subItem])
            @endforeach
        </ul>
    @endif
</li>