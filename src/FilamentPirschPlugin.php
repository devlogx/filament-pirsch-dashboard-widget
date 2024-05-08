<?php

namespace Devlogx\FilamentPirsch;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;

class FilamentPirschPlugin implements Plugin
{
    use EvaluatesClosures;
    protected bool | Closure | null $shouldShowPirschLink = null;
    protected string | Closure | null $pollingInterval = null;
    protected string | Closure | null $filterSectionIcon = null;
    protected string | Closure | null $filterSectionIconColor = null;

    protected string | Closure | null $liveVisitorIcon = null;
    protected string | Closure | null $liveVisitorColor = null;

    protected string | Closure | null $visitorsIcon = null;
    protected string | Closure | null $visitorsColor = null;

    protected string | Closure | null $viewsIcon = null;
    protected string | Closure | null $viewsColor = null;

    protected string | Closure | null $sessionTimeIcon = null;
    protected string | Closure | null $sessionTimeColor = null;
    public function getId(): string
    {
        return 'filament-pirsch-dashboard-widget';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function shouldShowPirschLink(): bool
    {
        return $this->evaluate($this->shouldShowPirschLink) ?? true;
    }

    public function pirschLink(bool | Closure | null $condition = true): static
    {
        $this->shouldShowPirschLink = $condition;
        return $this;
    }

    public function getPollingInterval(): string
    {
        return $this->evaluate($this->pollingInterval) ?? "60s";
    }

    public function pollingInterval(string | Closure | null $interval = "60s"): static
    {
        $this->pollingInterval = $interval;
        return $this;
    }

    public function getFilterSectionIcon(): string
    {
        return $this->evaluate($this->filterSectionIcon) ?? "heroicon-s-adjustments-vertical";
    }

    public function filterSectionIcon(string | Closure | null $icon = "heroicon-s-adjustments-vertical"): static
    {
        $this->filterSectionIcon = $icon;
        return $this;
    }

    public function getFilterSectionIconColor(): string
    {
        return $this->evaluate($this->filterSectionIconColor) ?? "primary";
    }

    public function filterSectionIconColor(string | Closure | null $color = "primary"): static
    {
        $this->filterSectionIconColor = $color;
        return $this;
    }

    public function getLiveVisitorIcon(): string
    {
        return $this->evaluate($this->liveVisitorIcon) ?? "heroicon-s-user";
    }

    public function liveVisitorIcon(string | Closure | null $icon = "heroicon-s-user"): static
    {
        $this->liveVisitorIcon = $icon;
        return $this;
    }

    public function getLiveVisitorColor(): string
    {
        return $this->evaluate($this->liveVisitorColor) ?? "primary";
    }

    public function liveVisitorColor(string | Closure | null $color = "primary"): static
    {
        $this->liveVisitorColor = $color;
        return $this;
    }

    public function getVisitorsIcon(): string
    {
        return $this->evaluate($this->visitorsIcon) ?? "heroicon-s-user-group";
    }

    public function visitorsIcon(string | Closure | null $icon = "heroicon-s-user-group"): static
    {
        $this->visitorsIcon = $icon;
        return $this;
    }

    public function getVisitorsColor(): string
    {
        return $this->evaluate($this->visitorsColor) ?? "primary";
    }

    public function visitorsColor(string | Closure | null $color = "primary"): static
    {
        $this->visitorsColor = $color;
        return $this;
    }

    public function getViewsIcon(): string
    {
        return $this->evaluate($this->viewsIcon) ?? "heroicon-s-eye";
    }

    public function viewsIcom(string | Closure | null $icon = "heroicon-s-eye"): static
    {
        $this->viewsIcon = $icon;
        return $this;
    }

    public function getViewsColor(): string
    {
        return $this->evaluate($this->viewsColor) ?? "primary";
    }

    public function viewsColor(string | Closure | null $color = "primary"): static
    {
        $this->viewsColor = $color;
        return $this;
    }

    public function getSessionTimeIcon(): string
    {
        return $this->evaluate($this->sessionTimeIcon) ?? "heroicon-s-clock";
    }

    public function sessionTimeIcon(string | Closure | null $icon = "heroicon-s-clock"): static
    {
        $this->sessionTimeIcon = $icon;
        return $this;
    }

    public function getSessionTimeColor(): string
    {
        return $this->evaluate($this->sessionTimeColor) ?? "primary";
    }

    public function sessionTimeColor(string | Closure | null $color = "primary"): static
    {
        $this->sessionTimeColor = $color;
        return $this;
    }
}
