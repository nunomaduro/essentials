<?php

declare(strict_types=1);

namespace NunoMaduro\Essentials\Commands;

use Illuminate\Console\Command;

final class ConfigureComposerCommand extends Command
{
    protected $signature = 'essentials:composer
        {--force : Force the operation to run without confirmation}
        {--backup : Create a backup of existing composer.json}';

    protected $description = 'This command will configure the composer scripts with a opinionated default.';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('Are you sure you want to update your composer.json', true)) {
            return 0;
        }

        $stub_path = __DIR__.'/../../stubs/composer.stub';
        $destination_path = base_path('composer.json');

        if (! file_exists($stub_path)) {
            $this->error('Composer scripts stub file not found.');

            return 1;
        }

        if (file_exists($destination_path) && $this->option('backup')) {
            copy($destination_path, $destination_path.'.backup');
            $this->info('Backup created at: '.$destination_path.'.backup');
        }

        if (! file_exists($destination_path)) {
            $this->error('composer.json file not found.');

            return 1;
        }

        $this->info('Adding composer scripts to your composer.json...');

        $original_composer_content = file_get_contents($destination_path);

        if ($original_composer_content === false) {
            $this->error('Failed to read composer.json file.');

            return 1;
        }

        $stub_content = file_get_contents($stub_path);

        if ($stub_content === false) {
            $this->error('Failed to read composer scripts stub file.');

            return 1;
        }

        $composer_json = (array) json_decode($original_composer_content, true);
        $composer_scripts = (array) json_decode($stub_content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Failed to decode composer.json file.');

            return 1;
        }

        $composer_json['scripts'] = array_merge((array) ($composer_json['scripts'] ?? []), $composer_scripts);

        if (file_put_contents($destination_path, json_encode($composer_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false) {
            $this->error('Failed to write to composer.json file.');

            return 1;
        }

        $this->info('Composer scripts added successfully.');

        return 0;
    }
}
