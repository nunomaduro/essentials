<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use NunoMaduro\Essentials\Commands\EssentialsRectorCommand;

beforeEach(function (): void {
    if (file_exists(base_path('rector.php'))) {
        unlink(base_path('rector.php'));
    }

    if (file_exists(base_path('rector.php.backup'))) {
        unlink(base_path('rector.php.backup'));
    }
});

it('publishes rector configuration file', function (): void {
    $command = new EssentialsRectorCommand();

    $this->artisan('essentials:rector', ['--force' => true])
        ->assertExitCode(0);

    expect(file_exists(base_path('rector.php')))->toBeTrue();
});

it('creates a backup when requested', function (): void {
    File::put(base_path('rector.php'), '<?php return [];');

    $this->artisan('essentials:rector', ['--backup' => true, '--force' => true])
        ->assertExitCode(0);

    expect(file_exists(base_path('rector.php.backup')))->toBeTrue();
});

it('warns when file exists and no force option', function (): void {
    File::put(base_path('rector.php'), '<?php return [];');

    $this->artisan('essentials:rector')
        ->expectsConfirmation('Do you wish to publish the Rector configuration file? This will override the existing [rector.php] file.', 'no')
        ->assertExitCode(0);

    expect(file_get_contents(base_path('rector.php')))->toBe('<?php return [];');
});

afterEach(function (): void {
    if (file_exists(base_path('rector.php'))) {
        unlink(base_path('rector.php'));
    }

    if (file_exists(base_path('rector.php.backup'))) {
        unlink(base_path('rector.php.backup'));
    }
});
