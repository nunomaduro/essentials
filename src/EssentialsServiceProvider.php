<?php

declare(strict_types=1);

namespace NunoMaduro\Essentials;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use NunoMaduro\Essentials\Contracts\Configurable;

/**
 * @internal
 */
final class EssentialsServiceProvider extends BaseServiceProvider
{
    /**
     * The list of configurables.
     *
     * @var list<class-string<Configurable>>
     */
    private array $configurables = [
        Configurables\AggressivePrefetching::class,
        Configurables\AutomaticallyEagerLoadRelationships::class,
        Configurables\FakeSleep::class,
        Configurables\ForceScheme::class,
        Configurables\ImmutableDates::class,
        Configurables\PreventStrayRequests::class,
        Configurables\ProhibitDestructiveCommands::class,
        Configurables\SetDefaultPassword::class,
        Configurables\ShouldBeStrict::class,
        Configurables\Unguard::class,
    ];

    /**
     * The list of commands.
     *
     * @var list<class-string<Command>>
     */
    private array $commandsList = [
        Commands\EssentialsRectorCommand::class,
        Commands\EssentialsPintCommand::class,
        Commands\MakeActionCommand::class,
    ];

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        collect($this->configurables)
            ->map(fn (string $configurable) => $this->app->make($configurable))
            ->filter(fn (Configurable $configurable): bool => $configurable->enabled())
            ->each(fn (Configurable $configurable) => $configurable->configure());

        if ($this->app->runningInConsole()) {
            $this->commands($this->commandsList);

            $this->publishes([
                __DIR__.'/../stubs' => $this->app->basePath('stubs'),
            ], 'essentials-stubs');

            $this->publishes([
                __DIR__.'/../config/essentials.php' => config_path('essentials.php'),
            ], 'essentials-config');
        }
    }
}
