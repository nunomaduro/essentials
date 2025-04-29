<?php

declare(strict_types=1);

namespace NunoMaduro\Essentials\Commands;

use Illuminate\Console\Command;

final class EssentialsRectorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'essentials:rector
        {--force : Force the operation to run without confirmation}
        {--backup : Create a backup of existing rector.php}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will publish an opinionated Rector configuration file for your project.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->option('force') && ! $this->components->confirm('Do you wish to publish the Rector configuration file? This will override the existing [rector.php] file.', true)) {
            return 0;
        }

        $stub_path = __DIR__.'/../../stubs/rector.stub';
        $destination_path = base_path('rector.php');

        if (! file_exists($stub_path)) {
            $this->components->error('Rector configuration stub file not found.');

            return 1;
        }

        if (file_exists($destination_path) && $this->option('backup')) {
            copy($destination_path, $destination_path.'.backup');
            $this->components->info('Backup created at: '.$destination_path.'.backup');
        }

        $this->components->info('Publishing Rector configuration file...');

        if (! copy($stub_path, $destination_path)) {
            $this->components->error('Failed to publish the Rector configuration file.');

            return 1;
        }

        $this->components->info('Rector configuration file published successfully at: '.$destination_path);

        return 0;
    }
}
