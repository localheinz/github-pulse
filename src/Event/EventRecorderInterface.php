<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @link https://github.com/localheinz/github-pulse
 */

namespace Localheinz\GitHub\Pulse\Event;

interface EventRecorderInterface
{
    public function record(EventInterface ...$events): void;

    /**
     * @param \Closure $filter
     *
     * @return EventInterface[]
     */
    public function filter(\Closure $filter): array;

    public function sort(): void;
}
