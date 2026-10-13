<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Saade\Facehash\Facehash;
use Saade\FilamentFacehash\FacehashPlugin;
use Saade\FilamentFacehash\FacehashProvider;
use Saade\FilamentFacehash\Tests\Fixtures\Member;
use Saade\FilamentFacehash\Tests\Fixtures\PluginlessPanelProvider;
use Saade\FilamentFacehash\Tests\Fixtures\User;

it('exposes the facehash of the model name as an attribute', function () {
    $member = new Member(['name' => 'Saade']);

    expect($member->facehash_avatar_name)->toBe('Saade')
        ->and($member->facehash_avatar_url)->toBe(app(Facehash::class)->name('Saade')->toUri());
});

it('lets the model choose another attribute as the avatar name', function () {
    $member = new class(['name' => 'Saade', 'email' => 'saade@example.com']) extends Member
    {
        public function facehashAvatarName(): Attribute
        {
            return new Attribute(
                get: fn () => $this->email
            );
        }
    };

    expect($member->facehash_avatar_url)->toBe(app(Facehash::class)->name('saade@example.com')->toUri());
});

it('is the avatar filament shows for a model that has one', function () {
    $member = new Member(['name' => 'Saade']);

    expect($member->getFilamentAvatarUrl())->toBe($member->facehash_avatar_url)
        ->and(Filament::getUserAvatarUrl($member))->toBe($member->facehash_avatar_url);
});

it('matches the avatar the provider draws for the same name', function () {
    FacehashPlugin::get()->size(96)->variant('solid')->format('square')->colors(['#111111']);

    expect((new Member(['name' => 'Saade']))->facehash_avatar_url)
        ->toBe((new FacehashProvider)->get(new User(['name' => 'Saade'])))
        ->not->toBe(app(Facehash::class)->name('Saade')->toUri());
});

it('draws a placeholder for a model without a name', function (mixed $name) {
    expect((new Member(['name' => $name]))->facehash_avatar_url)
        ->toBe(app(Facehash::class)->name('?')->toUri());
})->with([
    'null' => [null],
    'empty' => [''],
]);

it('works where there is no plugin to ask', function (array $panels) {
    $this->bootWithPanels($panels);

    expect((new Member(['name' => 'Saade']))->facehash_avatar_url)
        ->toBe(app(Facehash::class)->name('Saade')->toUri());
})->with([
    'panel without the plugin' => [[PluginlessPanelProvider::class]],
    'no panel at all' => [[]],
]);
