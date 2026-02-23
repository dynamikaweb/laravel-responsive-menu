dynamikasolucoesweb/laravel-responsive-menu
=========================
![php version](https://img.shields.io/packagist/php-v/dynamikasolucoesweb/laravel-responsive-menu)
![pkg version](https://img.shields.io/packagist/v/dynamikasolucoesweb/laravel-responsive-menu)
![license](https://img.shields.io/packagist/l/dynamikasolucoesweb/laravel-responsive-menu)
![quality](https://img.shields.io/scrutinizer/quality/g/dynamikaweb/laravel-responsive-menu)
![build](https://img.shields.io/scrutinizer/build/g/dynamikaweb/laravel-responsive-menu)

O Laravel Responsive Menu é um componente Blade que transforma arrays complexos em menus responsivos e auto-ajustáveis, utilizando o motor de animação DLMenu.

Instalação
------------
A maneira preferida de instalar esta extensão é através do [composer] [composer](http://getcomposer.org/download/).

Ou corre

```SHELL
$ composer require dynamikasolucoesweb/laravel-responsive-menu "*"
```

ou adicione

```JSON
"dynamikasolucoesweb/laravel-responsive-menu": "*"
```

à seção `require` do seu arquivo `composer.json`.

Assets & Customização
------------
Por padrão, a biblioteca injeta automaticamente o CSS e JS necessários. 

Caso deseje alterar os estilos (CSS) ou o comportamento do JS, publique os assets:

```SHELL
$ php artisan vendor:publish --tag=responsive-menu-assets
```

Isso copiará os arquivos para public/vendor/dynamikasolucoesweb/responsive-menu. A biblioteca passará a usar esses arquivos automaticamente, melhorando a performance.

## ⚠️ Requisito do Layout
Para que os estilos e scripts sejam injetados automaticamente, seu arquivo de layout base **precisa** conter a diretiva `@responsiveMenuAssets`:

```html
<head>
    {{-- Injeta automaticamente o CSS e JS do Menu Responsivo --}}
    @responsiveMenuAssets
    
    @stack('css')
</head>
<body>
    <x-responsive-menu :items="$menuTree" />
    
    @stack('scripts')
</body>
```

Uso
------------
Certifique-se de que seu layout principal possua a diretiva @responsiveMenuAssets. Basta chamar o componente e passar o seu array de itens. O componente gerencia o cache e a normalização dos dados automaticamente.

```HTML
<x-responsive-menu :items="$menuTree" />
```

Estrutura do Array
------------
O componente aceita uma estrutura de árvore. Abaixo, um exemplo de como formatar os dados (seja via Model ou Array estático):

```PHP
$menuTree = [
    [
        'label' => 'Institucional',
        'url' => '/quem-somos',
        'target' => '_self',
        'items' => [
            [
                'label' => 'Nossa História',
                'url' => '/historia',
                'target' => '_self',
                'items' => []
            ],
            [
                'label' => 'Equipe',
                'url' => '/equipe',
                'target' => '_self',
                'items' => []
            ],
        ]
    ],
    [
        'label' => 'Serviços',
        'url' => '/#',
        'target' => '_self',
        'content' => '<p>Texto customizado ou HTML</p>', // Conteúdo opcional
        'items' => [
            [
                'label' => 'Desenvolvimento Web',
                'url' => '/dev',
                'target' => '_blank',
                'content' => '<p>Texto customizado ou HTML</p>', // Conteúdo opcional
                'items' => []
            ],
            [
                'label' => 'Design',
                'url' => '/design',
                'target' => '_self',
                'items' => []
            ],
        ]
    ]
];
```

## ⚡ Performance & Cache
O componente utiliza uma camada de cache inteligente que se adapta ao ambiente:
- **Production**: Cache automático de 24h. O cache é invalidado automaticamente se os itens do menu forem alterados ou se o arquivo da biblioteca for atualizado.
- **Development**: Se APP_DEBUG=true, o cache é ignorado para refletir mudanças instantâneas.

Features
------------
Auto-Correction: Redistribui itens excedentes para manter a integridade do layout.

Smart Assets: Fallback automático entre rotas PHP e arquivos estáticos na pasta public.

Compatibilidade: Testado com jQuery 3.6.0+ e Modernizr custom.

Smart Caching: Cache de 24h em produção, com invalidação automática em caso de mudanças.

Authors
------------
Giordani da Silveira dos Santos - giordani@dynamika.com.br

--------------------------------------------------------------------------------------------------------------
[![dynamika soluções web](https://avatars.githubusercontent.com/dynamikaweb?size=12)](https://dynamika.com.br)
This project is under [BSD-3-Clause](https://opensource.org/licenses/BSD-3-Clause) license.