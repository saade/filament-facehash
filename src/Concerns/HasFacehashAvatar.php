<?php

declare(strict_types=1);

namespace Saade\FilamentFacehash\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Saade\FilamentFacehash\FacehashPlugin;

trait HasFacehashAvatar
{
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->facehash_avatar_url;
    }

    public function facehashAvatarName(): Attribute
    {
        return new Attribute(
            get: fn () => $this->name
        );
    }

    public function facehashAvatarUrl(): Attribute
    {
        return new Attribute(
            get: fn () => FacehashPlugin::current()
                ->avatar((string) $this->facehash_avatar_name)
                ->toUri()
        );
    }
}
