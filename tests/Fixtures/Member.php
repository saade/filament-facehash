<?php

declare(strict_types=1);

namespace Saade\FilamentFacehash\Tests\Fixtures;

use Filament\Models\Contracts\HasAvatar;
use Saade\FilamentFacehash\Concerns\HasFacehashAvatar;

class Member extends User implements HasAvatar
{
    use HasFacehashAvatar;
}
