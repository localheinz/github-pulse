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

final class EventRecorder implements EventRecorderInterface
{
    /**
     * @var EventInterface[]
     */
    private $events = [];

    /**
     * @var bool
     */
    private $isSorted = true;

    public function record(EventInterface ...$events): void
    {
        if ([] !== $this->events || 1 < \count($events)) {
            $this->isSorted = false;
        }

        foreach ($events as $event) {
            $this->events[] = $event;
        }
    }

    public function isSorted(): bool
    {
        return $this->isSorted;
    }

    public function sort(): void
    {
        if ($this->isSorted) {
            return;
        }

        \usort($this->events, static function (EventInterface $a, EventInterface $b) {
            return \strcmp(
                $a->time(),
                $b->time()
            );
        });

        $this->isSorted = true;
    }

    public function toArray(): array
    {
        return $this->events;
    }

    public function filter(\Closure $filter): array
    {
        return \array_filter(
            $this->events,
            $filter
        );
    }
}
