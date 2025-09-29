<?php

declare(strict_types=1);

namespace NunoMaduro\Essentials\Configurables;

use NunoMaduro\Essentials\Contracts\Configurable;

final readonly class MakeAction implements Configurable
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
        // Commands are registered in the service provider, not here
    }
}
