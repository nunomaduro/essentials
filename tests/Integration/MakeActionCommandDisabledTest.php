<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;

/**
 * Test case for verifying MakeAction command behavior when disabled.
 * Uses PHPUnit directly to properly test command registration prevention.
 */
final class MakeActionCommandDisabledTest extends Tests\TestCase
{
    public function test_make_action_command_is_not_registered_when_disabled(): void
    {
        // Verify the command is not registered when disabled
        $commands = array_keys(Artisan::all());

        $this->assertNotContains('make:action', $commands);
    }

    public function test_make_action_configurable_reports_disabled(): void
    {
        $makeActionConfigurable = $this->app->make(NunoMaduro\Essentials\Configurables\MakeAction::class);

        $this->assertFalse($makeActionConfigurable->enabled());
    }

    /**
     * Define environment setup to disable MakeAction before service provider boots.
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('essentials.'.NunoMaduro\Essentials\Configurables\MakeAction::class, false);
    }
}
