<?php

declare(strict_types=1);

namespace Saade\FilamentFacehash;

use Filament\Contracts\Plugin;
use Filament\Exceptions\NoDefaultPanelSetException;
use Filament\Panel;
use Saade\Facehash\Enums\Format;
use Saade\Facehash\Enums\Variant;
use Saade\Facehash\Facehash;

class FacehashPlugin implements Plugin
{
    protected ?int $size = null;

    protected ?Variant $variant = null;

    protected ?Format $format = null;

    protected ?bool $initial = null;

    protected ?bool $blink = null;

    protected ?array $colors = null;

    public static function make(): static
    {
        return new static;
    }

    public static function get(): static
    {
        return filament('facehash');
    }

    /**
     * The plugin of the current panel, or an unconfigured one where there is
     * none to ask: a panel without the plugin, or no panel at all.
     */
    public static function current(): static
    {
        try {
            $panel = filament()->getCurrentPanel() ?? filament()->getDefaultPanel();
        } catch (NoDefaultPanelSetException) {
            return static::make();
        }

        return $panel->hasPlugin('facehash') ? $panel->getPlugin('facehash') : static::make();
    }

    public function getId(): string
    {
        return 'facehash';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}

    public function size(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): int
    {
        return $this->size ?? (int) config('facehash.defaults.size', 40);
    }

    public function variant(string|Variant $variant): static
    {
        $this->variant = $variant instanceof Variant
            ? $variant
            : (Variant::tryFrom($variant) ?? Variant::Gradient);

        return $this;
    }

    public function getVariant(): Variant
    {
        return $this->variant
            ?? Variant::tryFrom((string) config('facehash.defaults.variant', 'gradient'))
            ?? Variant::Gradient;
    }

    public function format(string|Format $format): static
    {
        $this->format = $format instanceof Format
            ? $format
            : (Format::tryFrom($format) ?? Format::Circle);

        return $this;
    }

    public function getFormat(): Format
    {
        return $this->format
            ?? Format::tryFrom((string) config('facehash.defaults.format', 'circle'))
            ?? Format::Circle;
    }

    public function initial(bool $initial = true): static
    {
        $this->initial = $initial;

        return $this;
    }

    public function getInitial(): bool
    {
        return $this->initial ?? (bool) config('facehash.defaults.initial', true);
    }

    public function blink(bool $blink = true): static
    {
        $this->blink = $blink;

        return $this;
    }

    public function getBlink(): bool
    {
        return $this->blink ?? (bool) config('facehash.defaults.blink', false);
    }

    public function colors(array $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    public function getColors(): ?array
    {
        return $this->colors;
    }

    public function avatar(string $name): Facehash
    {
        $facehash = app(Facehash::class)
            ->name(filled($name) ? $name : '?')
            ->size($this->getSize())
            ->variant($this->getVariant())
            ->format($this->getFormat())
            ->initial($this->getInitial())
            ->blink($this->getBlink());

        if ($colors = $this->getColors()) {
            $facehash = $facehash->colors($colors);
        }

        return $facehash;
    }
}
