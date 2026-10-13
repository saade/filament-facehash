<?php

declare(strict_types=1);

arch('no debugging calls are left in the package')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'print_r'])
    ->each->not->toBeUsed();

arch('the package uses strict types')
    ->expect('Saade\FilamentFacehash')
    ->toUseStrictTypes();
