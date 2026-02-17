<?php

declare(strict_types=1);

namespace Saade\FilamentFacehash\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Saade\Facehash\Facehash;

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
            get: fn () => app(Facehash::class)
                ->name($this->facehash_avatar_name)
                ->toUri()
        );
    }
}
