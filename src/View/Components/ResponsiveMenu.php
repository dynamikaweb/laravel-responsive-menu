<?php

namespace DynamikaSolucoesWeb\Responsive\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;

class ResponsiveMenu extends Component
{
    public $items;
    public $id;

    public function __construct($items = [])
    {
        $this->items = $items;
        $this->id = 'dl-menu';
    }

    public function render()
    {
        $renderHtml = fn() => view('responsive-menu::components.responsive-menu', [
            'normalizedItems' => $this->normalizeItems(),
            'id' => $this->id
        ])->render();

        $hash = md5(json_encode($this->items) . getlastmod() . $this->id);
        $ttl = config('app.debug') ? 0 : now()->addDay();

        $content = Cache::remember("responsive-menu-{$hash}", $ttl, $renderHtml);

        return view('responsive-menu::components.responsive-wrapper', compact('content'));
    }

    public function normalizeItems(): array
    {
        if (empty($this->items)) {
            return [];
        }

        return array_map(fn(array $root) => $this->transformRootItem($root), $this->items);
    }

    /**
     * Transforma o item de primeiro nível (Root).
     */
    protected function transformRootItem(array $root): array
    {
        $label = data_get($root, 'encode', true) ? e($root['label']) : $root['label'];
        
        $items = $this->transformSubItems(
            data_get($root, 'items', []), 
            $label, 
            data_get($root, 'content')
        );

        return [
            'label'  => $label,
            'url'    => data_get($root, 'url', 'javascript:;'),
            'target' => data_get($root, 'target', '_self'),
            'items'  => $items
        ];
    }

    /**
     * Processa a lista de sub-itens e o conteúdo HTML.
     */
    protected function transformSubItems(array $subs, string $label, ?string $content = null): array
    {
        $newSubs = [];

        // Se houver conteúdo, ele entra como o primeiro item do sub-menu
        if (!empty($content)) {
            $newSubs[] = [
                'label'   => $label,
                'url'     => 'javascript:;',
                'content' => $content,
                'items'   => []
            ];
        }

        foreach ($subs as $oldSub) {
            $newSubs[] = $this->formatSubMenu($oldSub);
        }

        return $newSubs;
    }

    /**
     * Formata a estrutura de um sub-menu e seus filhos de terceiro nível.
     */
    protected function formatSubMenu(array $sub): array
    {
        $children = data_get($sub, 'items', []);

        return [
            'label'  => data_get($sub, 'label'),
            'url'    => data_get($sub, 'url', 'javascript:;'),
            'target' => data_get($sub, 'target', '_self'),
            'items'  => array_map(fn(array $item) => [
                'label'  => data_get($item, 'label'),
                'url'    => data_get($item, 'url', 'javascript:;'),
                'target' => data_get($item, 'target', '_self'),
            ], $children)
        ];
    }
}