<?php

namespace Genero\Sage\CacheableViewLoaders;

use Genero\Sage\CacheableViewLoaders\Console\ViewLoaderCacheCommand;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use Illuminate\Support\Str;

class CacheableViewLoadersServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        parent::register();
        $this->registerMacros();

        $this->commands([
            ViewLoaderCacheCommand::class,
        ]);
    }

    public function registerMacros(): void
    {
        $app = $this->app;

        /**
         * Overrides Acorns makeLoader macro with one that uses compiled paths
         * relative to the cache folder rather than absolute so that the
         * filename is the same in all environments.
         *
         * @return string
         */
        View::macro('makeLoader', function () use ($app) {
            $compiledPath = $app['config']['view.compiled'];
            $compiledExtension = $app['config']->get('view.compiled_extension', 'php');
            $view = $this->getName();
            $path = $this->getPath();
            $id = md5(Str::after($this->getCompiled(), $compiledPath));

            $content = "<?= \\Roots\\view('{$view}', \$data ?? get_defined_vars())->render(); ?>"
                ."\n<?php /**PATH {$path} ENDPATH**/ ?>";

            if (! file_exists($loader = "{$compiledPath}/{$id}-loader.{$compiledExtension}")) {
                file_put_contents($loader, $content);
            }

            return $loader;
        });
    }
}
