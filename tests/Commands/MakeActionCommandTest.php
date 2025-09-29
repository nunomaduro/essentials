<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

function cleanup(): void
{
    $actionsPath = app_path('Actions');

    if (File::isDirectory($actionsPath)) {
        File::deleteDirectory($actionsPath);
    }

    $stubsPath = base_path('stubs');
    if (File::exists($stubsPath)) {
        File::deleteDirectory($stubsPath);
    }
}

beforeEach(fn () => cleanup());
afterEach(fn () => cleanup());

it('creates a new action file', function (): void {
    $actionName = 'CreateUserAction';
    $exitCode = Artisan::call('make:action', ['name' => $actionName]);

    expect($exitCode)->toBe(0);

    $expectedPath = app_path('Actions/'.$actionName.'.php');
    expect(File::exists($expectedPath))->toBeTrue();

    $content = File::get($expectedPath);

    expect($content)
        ->toContain('namespace App\Actions;')
        ->toContain('class '.$actionName)
        ->toContain('public function handle(): void');
});

it('fails when the action already exists', function (): void {
    $actionName = 'CreateUserAction';
    Artisan::call('make:action', ['name' => $actionName]);
    $exitCode = Artisan::call('make:action', ['name' => $actionName]);

    expect($exitCode)->toBe(1);
});

it('add suffix "Action" to action name if not provided', function (string $actionName): void {
    $exitCode = Artisan::call('make:action', ['name' => $actionName]);

    expect($exitCode)->toBe(0);

    $expectedPath = app_path('Actions/CreateUserAction.php');
    expect(File::exists($expectedPath))->toBeTrue();

    $content = File::get($expectedPath);

    expect($content)
        ->toContain('namespace App\Actions;')
        ->toContain('class CreateUserAction')
        ->toContain('public function handle(): void');
})->with([
    'CreateUser',
    'CreateUser.php',
]);

it('uses published stub when available', function (): void {
    $this->artisan('vendor:publish', ['--tag' => 'essentials-stubs'])
        ->assertSuccessful();

    $publishedStubPath = base_path('stubs/action.stub');
    $originalContent = File::get($publishedStubPath);
    File::put($publishedStubPath, $originalContent."\n// this is user modified stub");

    $actionName = 'TestPublishedStubAction';
    $this->artisan('make:action', ['name' => $actionName])
        ->assertSuccessful();

    $expectedPath = app_path('Actions/TestPublishedStubAction.php');
    expect(File::exists($expectedPath))->toBeTrue()
        ->and(File::get($expectedPath))->toContain(
            '// this is user modified stub'
        );
});

it('action command is not available when disabled in config', function (): void {
    // Test the configurable directly
    $configurable = new NunoMaduro\Essentials\Configurables\MakeAction();

    // Disable the MakeAction feature
    config()->set('essentials.'.NunoMaduro\Essentials\Configurables\MakeAction::class, false);

    // The configurable should report as disabled
    expect($configurable->enabled())->toBeFalse();
});

it('action command is available when enabled in config', function (): void {
    // Test the configurable directly
    $configurable = new NunoMaduro\Essentials\Configurables\MakeAction();

    // Enable the MakeAction feature
    config()->set('essentials.'.NunoMaduro\Essentials\Configurables\MakeAction::class, true);

    // The configurable should report as enabled
    expect($configurable->enabled())->toBeTrue();
});

it('make:action command works normally when MakeAction is enabled', function (): void {
    // This test passes because MakeAction is enabled by default in beforeEach
    config()->set('essentials.'.NunoMaduro\Essentials\Configurables\MakeAction::class, true);

    // Check if the command is registered
    $commands = array_keys(Artisan::all());
    expect($commands)->toContain('make:action');

    // Test that the command actually works
    $this->artisan('make:action', ['name' => 'TestEnabledAction'])
        ->assertSuccessful();

    expect(File::exists(app_path('Actions/TestEnabledAction.php')))->toBeTrue();
});
