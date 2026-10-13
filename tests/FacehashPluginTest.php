<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Saade\Facehash\Enums\Format;
use Saade\Facehash\Enums\Variant;
use Saade\Facehash\Facehash;
use Saade\FilamentFacehash\FacehashPlugin;
use Saade\FilamentFacehash\Tests\Fixtures\PluginlessPanelProvider;

it('is registered on the panel under the facehash id', function () {
    expect(FacehashPlugin::make()->getId())->toBe('facehash')
        ->and(Filament::hasPlugin('facehash'))->toBeTrue()
        ->and(FacehashPlugin::get())->toBe(Filament::getPlugin('facehash'));
});

it('resolves the plugin registered on the panel as the current one', function () {
    expect(FacehashPlugin::current())->toBe(FacehashPlugin::get());
});

it('resolves an unconfigured plugin when there is none to ask', function (array $panels) {
    $this->bootWithPanels($panels);

    expect(FacehashPlugin::current())->toBeInstanceOf(FacehashPlugin::class)
        ->getSize()->toBe(40)
        ->getColors()->toBeNull();
})->with([
    'panel without the plugin' => [[PluginlessPanelProvider::class]],
    'no panel at all' => [[]],
]);

it('starts from the facehash defaults', function () {
    $plugin = FacehashPlugin::make();

    expect($plugin->getSize())->toBe(40)
        ->and($plugin->getVariant())->toBe(Variant::Gradient)
        ->and($plugin->getFormat())->toBe(Format::Circle)
        ->and($plugin->getInitial())->toBeTrue()
        ->and($plugin->getBlink())->toBeFalse()
        ->and($plugin->getColors())->toBeNull();
});

it('starts from the defaults in the facehash config', function () {
    config()->set('facehash.defaults', ['size' => 64, 'variant' => 'solid', 'format' => 'squircle', 'initial' => false, 'blink' => true]);

    $plugin = FacehashPlugin::make();

    expect($plugin->getSize())->toBe(64)
        ->and($plugin->getVariant())->toBe(Variant::Solid)
        ->and($plugin->getFormat())->toBe(Format::Squircle)
        ->and($plugin->getInitial())->toBeFalse()
        ->and($plugin->getBlink())->toBeTrue();
});

it('ignores an unknown variant or format in the facehash config', function () {
    config()->set('facehash.defaults', ['variant' => 'holographic', 'format' => 'hexagon']);

    expect(FacehashPlugin::make())
        ->getVariant()->toBe(Variant::Gradient)
        ->getFormat()->toBe(Format::Circle);
});

it('is configured fluently', function () {
    $plugin = FacehashPlugin::make();

    $configured = $plugin
        ->size(64)
        ->variant(Variant::Solid)
        ->format(Format::Square)
        ->initial(false)
        ->blink()
        ->colors(['#111111', '#222222']);

    expect($configured)->toBe($plugin)
        ->and($plugin->getSize())->toBe(64)
        ->and($plugin->getVariant())->toBe(Variant::Solid)
        ->and($plugin->getFormat())->toBe(Format::Square)
        ->and($plugin->getInitial())->toBeFalse()
        ->and($plugin->getBlink())->toBeTrue()
        ->and($plugin->getColors())->toBe(['#111111', '#222222']);
});

it('prefers its own options over the facehash config', function () {
    config()->set('facehash.defaults', ['size' => 64, 'variant' => 'solid', 'format' => 'square', 'initial' => false, 'blink' => true]);

    $plugin = FacehashPlugin::make()->size(24)->variant('gradient')->format('circle')->initial()->blink(false);

    expect($plugin->getSize())->toBe(24)
        ->and($plugin->getVariant())->toBe(Variant::Gradient)
        ->and($plugin->getFormat())->toBe(Format::Circle)
        ->and($plugin->getInitial())->toBeTrue()
        ->and($plugin->getBlink())->toBeFalse();
});

it('accepts the variant as a string', function (string $variant, Variant $expected) {
    expect(FacehashPlugin::make()->variant($variant)->getVariant())->toBe($expected);
})->with([
    'solid' => ['solid', Variant::Solid],
    'gradient' => ['gradient', Variant::Gradient],
    'unknown falls back to gradient' => ['holographic', Variant::Gradient],
]);

it('accepts the format as a string', function (string $format, Format $expected) {
    expect(FacehashPlugin::make()->format($format)->getFormat())->toBe($expected);
})->with([
    'square' => ['square', Format::Square],
    'squircle' => ['squircle', Format::Squircle],
    'circle' => ['circle', Format::Circle],
    'unknown falls back to circle' => ['hexagon', Format::Circle],
]);

it('builds an avatar with its options applied', function () {
    $plugin = FacehashPlugin::make()
        ->size(96)
        ->variant('solid')
        ->format('squircle')
        ->initial(false)
        ->blink()
        ->colors(['#111111', '#222222']);

    $expected = app(Facehash::class)
        ->name('Saade')
        ->size(96)
        ->variant('solid')
        ->format('squircle')
        ->initial(false)
        ->blink()
        ->colors(['#111111', '#222222']);

    expect($plugin->avatar('Saade')->toSvg())->toBe($expected->toSvg());
});

it('draws a placeholder for a blank name', function (string $name) {
    expect(FacehashPlugin::make()->avatar($name)->toSvg())
        ->toBe(app(Facehash::class)->name('?')->toSvg())
        ->toContain('>?</text>');
})->with([
    'empty' => '',
    'whitespace' => '   ',
]);
