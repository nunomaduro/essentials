<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

function cleanupMakeQuery(): void
{
    $queriesPath = app_path('Queries');

    if (File::isDirectory($queriesPath)) {
        File::deleteDirectory($queriesPath);
    }

    $stubsPath = base_path('stubs');
    if (File::exists($stubsPath)) {
        File::deleteDirectory($stubsPath);
    }
}

beforeEach(fn () => cleanupMakeQuery());
afterEach(fn () => cleanupMakeQuery());

it('creates a new query file', function (): void {
    $queryName = 'GetUsersQuery';
    $exitCode = Artisan::call('make:query', ['name' => $queryName]);

    expect($exitCode)->toBe(0);

    $expectedPath = app_path('Queries/'.$queryName.'.php');
    expect(File::exists($expectedPath))->toBeTrue();

    $content = File::get($expectedPath);

    expect($content)
        ->toContain('namespace App\Queries;')
        ->toContain('class '.$queryName)
        ->toContain('public function get(): void');
});

it('fails when the query already exists', function (): void {
    $queryName = 'GetUsersQuery';
    Artisan::call('make:query', ['name' => $queryName]);
    $exitCode = Artisan::call('make:query', ['name' => $queryName]);

    expect($exitCode)->toBe(1);
});

it('add suffix "Query" to query name if not provided', function (string $queryName): void {
    $exitCode = Artisan::call('make:query', ['name' => $queryName]);

    expect($exitCode)->toBe(0);

    $expectedPath = app_path('Queries/GetUsersQuery.php');
    expect(File::exists($expectedPath))->toBeTrue();

    $content = File::get($expectedPath);

    expect($content)
        ->toContain('namespace App\Queries;')
        ->toContain('class GetUsersQuery')
        ->toContain('public function get(): void');
})->with([
    'GetUsers',
    'GetUsers.php',
]);

it('uses published stub when available', function (): void {
    $this->artisan('vendor:publish', ['--tag' => 'essentials-stubs'])
        ->assertSuccessful();

    $publishedStubPath = base_path('stubs/query.stub');
    $originalContent = File::get($publishedStubPath);
    File::put($publishedStubPath, $originalContent."\n// this is user modified stub");

    $queryName = 'TestPublishedStubQuery';
    $this->artisan('make:query', ['name' => $queryName])
        ->assertSuccessful();

    $expectedPath = app_path('Queries/TestPublishedStubQuery.php');
    expect(File::exists($expectedPath))->toBeTrue()
        ->and(File::get($expectedPath))->toContain(
            '// this is user modified stub'
        );
});
