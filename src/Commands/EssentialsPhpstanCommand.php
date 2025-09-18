<?php

declare(strict_types=1);

namespace NunoMaduro\Essentials\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

final class EssentialsPhpstanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'essentials:phpstan
        {--force : Force the operation to run without confirmation}
        {--backup : Create a backup of existing phpstan.neon}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will publish an opinionated PHPStan configuration file for your project.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->option('force') && ! $this->components->confirm('Do you wish to publish the PHPStan configuration file? This will override the existing [phpstan.neon] file.', true)) {
            return 0;
        }

        // Check if larastan is installed
        if (! $this->isLarastanInstalled()) {
            $this->components->info('Installing nunomaduro/larastan...');

            $result = Process::run('composer require nunomaduro/larastan --dev');

            if (! $result->successful()) {
                $this->components->error('Failed to install nunomaduro/larastan.');
                $this->components->error($result->errorOutput());

                return 1;
            }

            $this->components->info('nunomaduro/larastan installed successfully.');
        }

        $stub_path = __DIR__.'/../../stubs/phpstan.stub';
        $destination_path = base_path('phpstan.neon');

        if (! file_exists($stub_path)) {
            $this->components->error('PHPStan configuration stub file not found.');

            return 1;
        }

        if (file_exists($destination_path) && $this->option('backup')) {
            copy($destination_path, $destination_path.'.backup');
            $this->components->info('Backup created at: '.$destination_path.'.backup');
        }

        $this->components->info('Publishing PHPStan configuration file...');

        if (! copy($stub_path, $destination_path)) {
            $this->components->error('Failed to publish the PHPStan configuration file.');

            return 1;
        }

        $this->components->info('PHPStan configuration file published successfully at: '.$destination_path);

        return 0;
    }

    /**
     * Check if nunomaduro/larastan is installed.
     *
     * @phpstan-return bool
     */
    private function isLarastanInstalled(): bool
    {
        $composerJsonPath = base_path('composer.json');
        if (! file_exists($composerJsonPath)) {
            return false;
        }
        $jsonString = file_get_contents($composerJsonPath);
        if ($jsonString === false) {
            return false;
        }
        /** @var array{require?: array<string, string>, require-dev?: array<string, string>} $composerJson */
        $composerJson = json_decode($jsonString, true);

        return isset($composerJson['require-dev']['nunomaduro/larastan'])
            || isset($composerJson['require']['nunomaduro/larastan']);
    }
}
