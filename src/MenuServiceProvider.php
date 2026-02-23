<?php

namespace DynamikaSolucoesWeb\Responsive;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use DynamikaSolucoesWeb\Responsive\View\Components\ResponsiveMenu;

class MenuServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'responsive-menu');

        Blade::component('responsive-menu', ResponsiveMenu::class);

        $this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/dynamikasolucoesweb/responsive-menu'),
        ], 'responsive-menu-assets');

        Blade::directive('responsiveMenuAssets', function () {
            return "<?php
                \$cssFiles = ['style.css'];
                \$jsFiles  = ['modernizr.custom.js', 'dlmenu.js', 'script.js'];
                
                \$publicPath = 'vendor/dynamikasolucoesweb/responsive-menu';
                \$isPublished = file_exists(public_path(\$publicPath));
    
                foreach (\$cssFiles as \$file) {
                    \$url = \$isPublished 
                        ? asset(\$publicPath . '/css/' . \$file) 
                        : route('responsive-menu.assets', ['type' => 'css', 'file' => \$file]);
                    echo ' <link rel=\"stylesheet\" href=\"' . \$url . '\">' . PHP_EOL;
                }
    
                foreach (\$jsFiles as \$file) {
                    \$url = \$isPublished 
                        ? asset(\$publicPath . '/js/' . \$file) 
                        : route('responsive-menu.assets', ['type' => 'js', 'file' => \$file]);
                    echo ' <script src=\"' . \$url . '\"></script>' . PHP_EOL;
                }
            ?>";
        });

        $this->registerAssetRoutes();
    }

    protected function registerAssetRoutes()
    {
        Route::get('responsive-menu/assets/{type}/{file}', function ($type, $file) {
            $path = __DIR__.'/../resources/assets/' . $type . '/' . $file;
            
            if (!file_exists($path)) {
                abort(404);
            }

            $mimes = [
                'css' => 'text/css',
                'js'  => 'application/javascript',
            ];

            $contentType = $mimes[$type] ?? 'text/plain';

            return response()->file($path, [
                'Content-Type' => $contentType,
                'Cache-Control' => 'public, max-age=31536000',
            ]);
        })->name('responsive-menu.assets');
    }
}