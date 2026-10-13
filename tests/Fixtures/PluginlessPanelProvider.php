<?php

declare(strict_types=1);

namespace Saade\FilamentFacehash\Tests\Fixtures;

use Filament\Panel;
use Filament\PanelProvider as BasePanelProvider;
use Saade\FilamentFacehash\FacehashProvider;

class PluginlessPanelProvider extends BasePanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->defaultAvatarProvider(FacehashProvider::class);
    }
}
