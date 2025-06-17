<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

it('creates a new service file', function (): void {
    $serviceName = 'UserService';
    $exitCode = Artisan::call('make:service', ['name' => $serviceName]);

    expect($exitCode)->toBe(0);

    $expectedPath = app_path('Services/'.$serviceName.'.php');
    expect(File::exists($expectedPath))->toBeTrue();

    $content = File::get($expectedPath);

    expect($content)
        ->toContain('namespace App\Services;')
        ->toContain('class '.$serviceName)
        ->toContain('public function __construct(){}');
});

it('fails when the service already exists', function (): void {
    $serviceName = 'UserService';
    Artisan::call('make:service', ['name' => $serviceName]);
    $exitCode = Artisan::call('make:service', ['name' => $serviceName]);

    expect($exitCode)->toBe(1);
});

it('add suffix "Service" to service name if not provided', function (string $serviceName): void {
    $exitCode = Artisan::call('make:service', ['name' => $serviceName]);

    expect($exitCode)->toBe(0);

    $expectedPath = app_path('Services/UserService.php');
    expect(File::exists($expectedPath))->toBeTrue();

    $content = File::get($expectedPath);

    expect($content)
        ->toContain('namespace App\Services;')
        ->toContain('class UserService')
        ->toContain('public function __construct(){}');
})->with([
    'UserService',
    'UserService.php',
]);

it('uses published stub when available', function (): void {
    $this->artisan('vendor:publish', ['--tag' => 'essentials-stubs'])
        ->assertSuccessful();

    $publishedStubPath = base_path('stubs/service.stub');
    $originalContent = File::get($publishedStubPath);
    File::put($publishedStubPath, $originalContent."\n// this is user modified stub");

    $serviceName = 'TestPublishedStubService';
    $this->artisan('make:service', ['name' => $serviceName])
        ->assertSuccessful();

    $expectedPath = app_path('Services/TestPublishedStubService.php');
    expect(File::exists($expectedPath))->toBeTrue()
        ->and(File::get($expectedPath))->toContain(
            '// this is user modified stub'
        );
});
