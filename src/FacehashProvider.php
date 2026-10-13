<?php

declare(strict_types=1);

namespace Saade\FilamentFacehash;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class FacehashProvider implements AvatarProvider
{
    public function get(Model|Authenticatable $record): string
    {
        return FacehashPlugin::current()
            ->avatar(Filament::getNameForDefaultAvatar($record))
            ->toUri();
    }
}
