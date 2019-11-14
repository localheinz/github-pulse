<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/github-pulse
 */

namespace Localheinz\GitHub\Pulse\Event;

interface EventRecorderInterface
{
    public function record(EventInterface ...$events): void;

    public function isSorted(): bool;

    public function sort(): void;

    /**
     * @return EventInterface[]
     */
    public function toArray(): array;

    /**
     * @param \Closure $filter
     *
     * @return EventInterface[]
     */
    public function filter(\Closure $filter): array;
}
