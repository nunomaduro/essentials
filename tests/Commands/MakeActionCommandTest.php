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
    $actionName = 'CreateUser';
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
    $actionName = 'CreateUser';
    Artisan::call('make:action', ['name' => $actionName]);
    $exitCode = Artisan::call('make:action', ['name' => $actionName]);

    expect($exitCode)->toBe(1);
});

it('does not append an Action suffix', function (string $actionName): void {
    $exitCode = Artisan::call('make:action', ['name' => $actionName]);

    expect($exitCode)->toBe(0);

    $expectedPath = app_path('Actions/CreateUser.php');
    expect(File::exists($expectedPath))->toBeTrue();
    expect(File::exists(app_path('Actions/CreateUserAction.php')))->toBeFalse();

    $content = File::get($expectedPath);

    expect($content)
        ->toContain('namespace App\Actions;')
        ->toContain('class CreateUser')
        ->toContain('public function handle(): void');
})->with([
    'CreateUser',
    'CreateUser.php',
]);

it('uses the name as provided when it already ends with Action', function (): void {
    $exitCode = Artisan::call('make:action', ['name' => 'CreateUserAction']);

    expect($exitCode)->toBe(0);

    $expectedPath = app_path('Actions/CreateUserAction.php');
    expect(File::exists($expectedPath))->toBeTrue();

    expect(File::get($expectedPath))
        ->toContain('class CreateUserAction');
});

it('uses published stub when available', function (): void {
    $this->artisan('vendor:publish', ['--tag' => 'essentials-stubs'])
        ->assertSuccessful();

    $publishedStubPath = base_path('stubs/action.stub');
    $originalContent = File::get($publishedStubPath);
    File::put($publishedStubPath, $originalContent."\n// this is user modified stub");

    $actionName = 'TestPublishedStub';
    $this->artisan('make:action', ['name' => $actionName])
        ->assertSuccessful();

    $expectedPath = app_path('Actions/TestPublishedStub.php');
    expect(File::exists($expectedPath))->toBeTrue()
        ->and(File::get($expectedPath))->toContain(
            '// this is user modified stub'
        );
});
