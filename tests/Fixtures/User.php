<?php

declare(strict_types=1);

namespace Saade\FilamentFacehash\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $guarded = [];
}
