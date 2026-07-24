<?php

declare(strict_types=1);

namespace NunoMaduro\Essentials\Configurables;

use Illuminate\Foundation\Http\FormRequest;
use NunoMaduro\Essentials\Contracts\Configurable;

final readonly class FailOnUnknownFields implements Configurable
{
    /**
     * Whether the configurable is enabled or not.
     */
    public function enabled(): bool
    {
        return config()->boolean(sprintf('essentials.%s', self::class), true);
    }

    /**
     * Run the configurable.
     */
    public function configure(): void
    {
        if (! method_exists(FormRequest::class, 'failOnUnknownFields')) { // @phpstan-ignore function.alreadyNarrowedType
            return;
        }

        FormRequest::failOnUnknownFields();
    }
}
