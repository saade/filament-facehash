<?php

declare(strict_types=1);

namespace Saade\FilamentFacehash;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Saade\Facehash\Facehash;

class FacehashProvider implements AvatarProvider
{
    public function get(Model|Authenticatable $record): string
    {
        $plugin = FacehashPlugin::get();

        $builder = app(Facehash::class)
            ->name($record->name)
            ->size($plugin->getSize())
            ->variant($plugin->getVariant())
            ->initial($plugin->getInitial());

        if ($colors = $plugin->getColors()) {
            $builder = $builder->colors($colors);
        }

        return $builder->toUri();
    }
}
