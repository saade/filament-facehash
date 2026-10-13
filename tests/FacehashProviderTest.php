<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Filament\Models\Contracts\HasName;
use Saade\Facehash\Facehash;
use Saade\FilamentFacehash\FacehashPlugin;
use Saade\FilamentFacehash\FacehashProvider;
use Saade\FilamentFacehash\Tests\Fixtures\PluginlessPanelProvider;
use Saade\FilamentFacehash\Tests\Fixtures\Team;
use Saade\FilamentFacehash\Tests\Fixtures\User;

function facehash(string $name): Facehash
{
    return app(Facehash::class)->name($name);
}

it('renders the facehash of the record name as a data uri', function () {
    expect((new FacehashProvider)->get(new User(['name' => 'Saade'])))
        ->toStartWith('data:image/svg+xml;base64,')
        ->toBe(facehash('Saade')->toUri());
});

it('gives the same record the same avatar and different records different ones', function () {
    $provider = new FacehashProvider;

    expect($provider->get(new User(['name' => 'Saade'])))
        ->toBe($provider->get(new User(['name' => 'Saade'])))
        ->not->toBe($provider->get(new User(['name' => 'Someone else'])));
});

it('is the avatar filament shows for a user', function () {
    expect(Filament::getUserAvatarUrl(new User(['name' => 'Saade'])))->toBe(facehash('Saade')->toUri());
});

it('is the avatar filament shows for a tenant', function () {
    expect(Filament::getTenantAvatarUrl(new Team(['name' => 'Acme'])))->toBe(facehash('Acme')->toUri());
});

it('uses the filament name of a record that has no name attribute', function () {
    $user = new class(['first_name' => 'Ada', 'last_name' => 'Lovelace']) extends User implements HasName
    {
        public function getFilamentName(): string
        {
            return "{$this->first_name} {$this->last_name}";
        }
    };

    expect((new FacehashProvider)->get($user))->toBe(facehash('Ada Lovelace')->toUri());
});

it('accepts a name that is not a string', function () {
    expect((new FacehashProvider)->get(new User(['name' => 42])))->toBe(facehash('42')->toUri());
});

it('draws a placeholder for a record with a blank name', function () {
    expect((new FacehashProvider)->get(new User(['name' => ''])))->toBe(facehash('?')->toUri());
});

it('applies the options set on the plugin', function () {
    FacehashPlugin::get()
        ->size(96)
        ->variant('solid')
        ->format('squircle')
        ->initial(false)
        ->blink()
        ->colors(['#111111', '#222222']);

    $expected = facehash('Saade')
        ->size(96)
        ->variant('solid')
        ->format('squircle')
        ->initial(false)
        ->blink()
        ->colors(['#111111', '#222222'])
        ->toUri();

    expect((new FacehashProvider)->get(new User(['name' => 'Saade'])))->toBe($expected);
});

it('applies each plugin option on its own', function (string $option, mixed $value) {
    FacehashPlugin::get()->{$option}($value);

    expect((new FacehashProvider)->get(new User(['name' => 'Saade'])))
        ->toBe(facehash('Saade')->{$option}($value)->toUri())
        ->not->toBe(facehash('Saade')->toUri());
})->with([
    'size' => ['size', 96],
    'variant' => ['variant', 'solid'],
    'format' => ['format', 'square'],
    'initial' => ['initial', false],
    'blink' => ['blink', true],
    'colors' => ['colors', ['#111111']],
]);

it('keeps the facehash palette when the plugin is given no colors', function () {
    FacehashPlugin::get()->colors([]);

    expect((new FacehashProvider)->get(new User(['name' => 'Saade'])))->toBe(facehash('Saade')->toUri());
});

it('follows the facehash config when the plugin sets nothing', function () {
    config()->set('facehash', [
        'defaults' => ['size' => 64, 'variant' => 'solid', 'format' => 'square', 'initial' => false, 'blink' => true],
        'colors' => ['#123456'],
    ]);

    $svg = base64_decode(substr((new FacehashProvider)->get(new User(['name' => 'Saade'])), strlen('data:image/svg+xml;base64,')));

    expect($svg)
        ->toContain('<rect width="64" height="64" fill="#123456"/>')
        ->toContain('<style>')
        ->not->toContain('radialGradient')
        ->not->toContain('<text');
});

it('works on a panel that does not register the plugin', function () {
    $this->bootWithPanels([PluginlessPanelProvider::class]);

    expect(Filament::getUserAvatarUrl(new User(['name' => 'Saade'])))->toBe(facehash('Saade')->toUri());
});
