<?php

namespace Genero\Sage\CacheableViewLoaders\Console;

use Illuminate\Foundation\Console\ViewCacheCommand;
use Illuminate\Support\Collection;
use Roots\Acorn\View\FileViewFinder;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;

use function Roots\view;

class ViewLoaderCacheCommand extends ViewCacheCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'viewloader:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compile all blade templates as view loaders.';

    /**
     * {@inheritDoc}
     */
    public function handle(): void
    {
        $this->paths()->each(function ($path) {
            $prefix = $this->output->isVeryVerbose() ? '<fg=yellow;options=bold>DIR</> ' : '';
            $this->components->task($prefix.$path, null, OutputInterface::VERBOSITY_VERBOSE);
            $this->compileLoaders($this->bladeFilesIn([$path]));
        });

        $this->newLine();
        $this->components->info('Loader templates cached successfully.');
    }


    /**
     * {@inheritDoc}
     */
    protected function compileLoaders(Collection $views): void
    {
        /** @var FileViewFinder $viewFinder */
        $viewFinder = $this->laravel->make(FileViewFinder::class);

        $views->map(function (SplFileInfo $file) use ($viewFinder) {
            $this->components->task('    '.$file->getRelativePathname(), null, OutputInterface::VERBOSITY_VERY_VERBOSE);

            view(
                $viewFinder->getPossibleViewNameFromPath($file->getRealPath())
            )->makeLoader();
        });

        if ($this->output->isVeryVerbose()) {
            $this->newLine();
        }
    }
}
