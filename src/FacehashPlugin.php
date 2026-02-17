<?php

declare(strict_types=1);

namespace Saade\FilamentFacehash;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Saade\Facehash\Enums\Variant;

class FacehashPlugin implements Plugin
{
    protected ?int $size = null;

    protected ?Variant $variant = null;

    protected ?bool $initial = null;

    protected ?array $colors = null;

    public static function make(): static
    {
        return new static;
    }

    public static function get(): static
    {
        return filament('facehash');
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
        return $this->size ?? 40;
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
        return $this->variant ?? Variant::Gradient;
    }

    public function initial(bool $initial = true): static
    {
        $this->initial = $initial;

        return $this;
    }

    public function getInitial(): bool
    {
        return $this->initial ?? true;
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
}
