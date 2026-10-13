<?php

declare(strict_types=1);

namespace Saade\FilamentFacehash\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Saade\FilamentFacehash\FacehashServiceProvider;
use Saade\FilamentFacehash\Tests\Fixtures\PanelProvider;

abstract class TestCase extends Orchestra
{
    /** @var list<class-string> */
    protected array $panelProviders = [PanelProvider::class];

    protected function getPackageProviders($app): array
    {
        return [
            ...$this->installedPackageProviders(),
            FacehashServiceProvider::class,
            ...$this->panelProviders,
        ];
    }

    /**
     * Panels are registered while the application boots, so a different set
     * of them needs a fresh application.
     *
     * @param  list<class-string>  $providers
     */
    public function bootWithPanels(array $providers): static
    {
        $this->panelProviders = $providers;
        $this->reloadApplication();

        return $this;
    }

    /**
     * Filament ships a different set of service providers in every major
     * version, so they are read from Composer the way package discovery does
     * in a real app instead of being listed here.
     *
     * @return list<class-string>
     */
    protected function installedPackageProviders(): array
    {
        $installed = json_decode(file_get_contents(__DIR__ . '/../vendor/composer/installed.json'), true);

        return collect($installed['packages'])
            ->flatMap(fn (array $package) => $package['extra']['laravel']['providers'] ?? [])
            ->all();
    }
}
