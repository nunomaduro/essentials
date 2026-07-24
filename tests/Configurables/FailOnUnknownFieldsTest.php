<?php

declare(strict_types=1);

use Illuminate\Foundation\Http\FormRequest;
use NunoMaduro\Essentials\Configurables\FailOnUnknownFields;

it('enables fail on unknown fields for form requests', function (): void {
    $failOnUnknownFields = new FailOnUnknownFields;
    $failOnUnknownFields->configure();

    $reflection = new ReflectionClass(FormRequest::class);
    $property = $reflection->getProperty('globalFailOnUnknownFields');

    expect($property->getValue())->toBeTrue();
})->skip(
    fn (): bool => ! method_exists(FormRequest::class, 'failOnUnknownFields'),
    'failOnUnknownFields is not supported in this version of Laravel.'
);

it('is enabled by default', function (): void {
    $failOnUnknownFields = new FailOnUnknownFields;

    expect($failOnUnknownFields->enabled())->toBeTrue();
});

it('can be disabled via configuration', function (): void {
    config()->set('essentials.'.FailOnUnknownFields::class, false);

    $failOnUnknownFields = new FailOnUnknownFields;

    expect($failOnUnknownFields->enabled())->toBeFalse();
});

it('does nothing when failOnUnknownFields method does not exist', function (): void {
    $failOnUnknownFields = new FailOnUnknownFields;
    $failOnUnknownFields->configure();

    expect($failOnUnknownFields->enabled())->toBeTrue();
})->skip(
    fn (): bool => method_exists(FormRequest::class, 'failOnUnknownFields'),
    'failOnUnknownFields exists in this version of Laravel.'
);
