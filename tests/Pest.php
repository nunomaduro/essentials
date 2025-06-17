<?php

declare(strict_types=1);

use Tests\TestCase;

pest()->use(TestCase::class)
    ->in('Configurables');

pest()->use(TestCase::class)
    ->in('Commands')
    ->beforeEach(fn () => cleanup())
    ->afterEach(fn () => cleanup());

function cleanup(): void
{
    $makeCommandPaths = [
        app_path('Actions'),
        app_path('Services'),
    ];

    foreach ($makeCommandPaths as $path) {
        if (File::isDirectory($path)) {
            File::deleteDirectory($path);
        }
    }

    $stubsPath = base_path('stubs');
    if (File::exists($stubsPath)) {
        File::deleteDirectory($stubsPath);
    }
}
