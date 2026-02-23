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
        if (empty($this->items)) return [];

        $newRoots = [];
        foreach ($this->items as $oldRoot) {
            $label = data_get($oldRoot, 'encode', true) ? e($oldRoot['label']) : $oldRoot['label'];
            $newSubs = [];

            if (!empty($oldRoot['content'])) {
                $newSubs[] = [
                    'label' => $label,
                    'url' => 'javascript:;',
                    'content' => $oldRoot['content'],
                    'items' => []
                ];
            }

            foreach (data_get($oldRoot, 'items', []) as $oldSub) {
                $newSubs[] = [
                    'label' => data_get($oldSub, 'label'),
                    'url' => data_get($oldSub, 'url', 'javascript:;'),
                    'target' => data_get($oldSub, 'target', '_self'),
                    'items' => collect(data_get($oldSub, 'items', []))->map(fn($item) => [
                        'label' => data_get($item, 'label'),
                        'url' => data_get($item, 'url', 'javascript:;'),
                        'target' => data_get($item, 'target', '_self'),
                    ])->toArray()
                ];
            }

            $newRoots[] = [
                'label' => $label,
                'url' => data_get($oldRoot, 'url', 'javascript:;'),
                'target' => data_get($oldRoot, 'target', '_self'),
                'items' => $newSubs
            ];
        }
        return $newRoots;
    }
}