<?php

declare(strict_types=1);

namespace daandelange\SimpleStats;

use Kirby\Form\Field\DisplayField;
use Kirby\Reflection\Attributes\Prop;

class PagestatsField extends DisplayField {

    #[Prop]
    protected bool $showTotals = true;

    #[Prop]
    protected bool $showTimeline = false;

    #[Prop]
    protected bool $showLanguages = false;

    #[Prop]
    protected string $size = 'medium';

    public function props(): array {
        return [
            ...parent::props(),
            'statsdata' => $this->model()->getPageStats(),
        ];
    }
}
