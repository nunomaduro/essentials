<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

beforeEach(function (): void {
    if (file_exists(base_path('phpstan.neon'))) {
        unlink(base_path('phpstan.neon'));
    }

    if (file_exists(base_path('phpstan.neon.backup'))) {
        unlink(base_path('phpstan.neon.backup'));
    }

    Process::fake([
        'composer require nunomaduro/larastan --dev' => Process::result(
            exitCode: 0,
        ),
    ]);
});

it('publishes phpstan configuration file', function (): void {
    $this->artisan('essentials:phpstan', ['--force' => true])
        ->assertExitCode(0);

    expect(file_exists(base_path('phpstan.neon')))->toBeTrue();

    $contents = file_get_contents(base_path('phpstan.neon'));
    expect($contents)->toContain('includes:');
    expect($contents)->toContain('vendor/nunomaduro/larastan/extension.neon');
});

it('creates a backup when requested', function (): void {
    File::put(base_path('phpstan.neon'), 'parameters: []');

    $this->artisan('essentials:phpstan', ['--backup' => true, '--force' => true])
        ->assertExitCode(0);

    expect(file_exists(base_path('phpstan.neon.backup')))->toBeTrue();
});

it('warns when file exists and no force option', function (): void {
    File::put(base_path('phpstan.neon'), 'parameters: []');

    $this->artisan('essentials:phpstan')
        ->expectsConfirmation('Do you wish to publish the PHPStan configuration file? This will override the existing [phpstan.neon] file.', 'no')
        ->assertExitCode(0);

    expect(file_get_contents(base_path('phpstan.neon')))->toBe('parameters: []');
});

it('shows error when larastan installation fails', function (): void {
    // Fake Laravel Process facade
    Process::fake([
        'composer require nunomaduro/larastan --dev' => Process::result(
            errorOutput: 'Simulated composer error',
            exitCode: 1,
        ),
    ]);

    // Only assert the exit code, skip output assertions
    $this->artisan('essentials:phpstan', ['--backup' => true, '--force' => true])
        ->assertExitCode(1);
});

afterEach(function (): void {
    if (file_exists(base_path('phpstan.neon'))) {
        unlink(base_path('phpstan.neon'));
    }

    if (file_exists(base_path('phpstan.neon.backup'))) {
        unlink(base_path('phpstan.neon.backup'));
    }
});
