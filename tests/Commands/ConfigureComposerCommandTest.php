<?php

declare(strict_types=1);

beforeEach(function (): void {
    $composer_json = json_decode(file_get_contents(base_path('composer.json')), true);
    $composer_json['scripts'] = [];

    file_put_contents(base_path('composer.json'), json_encode($composer_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    if (file_exists(base_path('composer.json.backup'))) {
        unlink(base_path('composer.json.backup'));
    }
});

afterEach(function (): void {
    $composer_json = json_decode(file_get_contents(base_path('composer.json')), true);
    $composer_json['scripts'] = [];

    file_put_contents(base_path('composer.json'), json_encode($composer_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    if (file_exists(base_path('composer.json.backup'))) {
        unlink(base_path('composer.json.backup'));
    }
});

it('adds composer scripts to composer.json', function (): void {
    $this->artisan('essentials:composer', ['--force' => true])
        ->assertExitCode(0)
        ->assertSuccessful();

    $composerJson = json_decode(file_get_contents(base_path('composer.json')), true);

    expect($composerJson)->toHaveKey('scripts')
        ->and($composerJson['scripts'])->toHaveKey('refactor')
        ->and($composerJson['scripts'])->toHaveKey('lint')
        ->and($composerJson['scripts'])->toHaveKey('test:refactor')
        ->and($composerJson['scripts'])->toHaveKey('test:lint')
        ->and($composerJson['scripts'])->toHaveKey('test:types')
        ->and($composerJson['scripts'])->toHaveKey('test:unit')
        ->and($composerJson['scripts'])->toHaveKey('test');
});

it('creates a backup when backup option is provided', function (): void {
    $this->artisan('essentials:composer', ['--force' => true, '--backup' => true])
        ->assertExitCode(0)
        ->assertSuccessful();

    expect(file_exists(base_path('composer.json.backup')))->toBeTrue();
});

it('does not create a backup when backup option is not provided', function (): void {
    $this->artisan('essentials:composer', ['--force' => true])
        ->assertExitCode(0)
        ->assertSuccessful();

    expect(file_exists(base_path('composer.json.backup')))->toBeFalse();
});

it('returns early when not confirmed', function (): void {
    $this->artisan('essentials:composer')
        ->expectsConfirmation('Are you sure you want to update your composer.json', 'no')
        ->assertExitCode(0);

    $originalContent = json_decode(file_get_contents(base_path('composer.json')), true);
    expect($originalContent['scripts'])->not->toHaveKey('refactor');
});
