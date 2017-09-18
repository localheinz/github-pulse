<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017 Andreas Möller.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @link https://github.com/localheinz/github-pulse
 */

namespace Localheinz\GitHub\Pulse\Test\Unit\Event;

use Localheinz\GitHub\Pulse\Event;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

final class EventRecorderTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsEventRecorderInterface(): void
    {
        $this->assertClassImplementsInterface(
            Event\EventRecorderInterface::class,
            Event\EventRecorder::class
        );
    }

    public function testDefaults(): void
    {
        $recorder = new Event\EventRecorder();

        $this->assertTrue($recorder->isSorted());
        $this->assertInternalType('array', $recorder->toArray());
        $this->assertEmpty($recorder->toArray());
    }

    public function testRecordRecordsEvent(): void
    {
        $event = $this->createEventMock();

        $recorder = new Event\EventRecorder();

        $recorder->record($event);

        $this->assertInternalType('array', $recorder->toArray());
        $this->assertCount(1, $recorder->toArray());
        $this->assertContains($event, $recorder->toArray());
    }

    public function testRecordRecordsEvents(): void
    {
        $events = [
            $this->createEventMock(),
            $this->createEventMock(),
            $this->createEventMock(),
        ];

        $recorder = new Event\EventRecorder();

        $recorder->record(...$events);

        $this->assertSame(\array_values($events), $recorder->toArray());
    }

    public function testRecordEventOnEmptyRecorderRecorderSorted(): void
    {
        $event = $this->createEventMock();

        $recorder = new Event\EventRecorder();

        $recorder->record($event);

        $this->assertTrue($recorder->isSorted());
    }

    public function testRecordEventOnRecorderWithEventMarksRecorderUnsorted(): void
    {
        $event = $this->createEventMock();

        $recorder = new Event\EventRecorder();

        $recorder->record($event);
        $recorder->record($event);

        $this->assertFalse($recorder->isSorted());
    }

    public function testRecordEventsOnEmptyRecorderMarksRecorderUnsorted(): void
    {
        $events = [
            $this->createEventMock(),
            $this->createEventMock(),
            $this->createEventMock(),
        ];

        $recorder = new Event\EventRecorder();

        $recorder->record(...$events);

        $this->assertFalse($recorder->isSorted());
    }

    public function testSortDoesNotSortIfRecorderIsSorted(): void
    {
        $event = $this->createEventMock();

        $event
            ->expects($this->never())
            ->method($this->anything());

        $recorder = new Event\EventRecorder();

        $recorder->record($event);

        $recorder->sort();
    }

    public function testSortSortsEventsByTime(): void
    {
        $faker = $this->faker();

        $times = \array_map(function () use ($faker) {
            return $faker->dateTime->format('Y-m-d\TH:i:s\Z');
        }, \array_fill(0, 5, null));

        $events = \array_map(function (string $time) {
            return $this->createEventSpy($time);
        }, $times);

        $recorder = new Event\EventRecorder();

        $recorder->record(...$events);

        $sorted = \array_combine(
            $times,
            $events
        );

        \ksort($sorted);

        $recorder->sort();

        $this->assertSame(\array_values($sorted), $recorder->toArray());
    }

    public function testFilterIfNoEventsHaveBeenRecorded(): void
    {
        $recorder = new Event\EventRecorder();

        $filtered = $recorder->filter(function () {
            return true;
        });

        $this->assertInternalType('array', $filtered);
        $this->assertEmpty($filtered);
    }

    public function testFilterFiltersEvents(): void
    {
        $now = new \DateTimeImmutable();

        $past = $now->sub(new \DateInterval('P1Y'))->format('Y-m-d\TH:i:s\Z');
        $future = $now->add(new \DateInterval('P1Y'))->format('Y-m-d\TH:i:s\Z');

        $pastEvent = $this->createEventSpy($past);
        $futureEvent = $this->createEventSpy($future);

        $recorder = new Event\EventRecorder();

        $recorder->record($pastEvent);
        $recorder->record($futureEvent);

        $filtered = $recorder->filter(function (Event\EventInterface $event) use ($now) {
            return $event->time() <= $now->format('Y-m-d\TH:i:s\Z');
        });

        $this->assertInternalType('array', $filtered);
        $this->assertCount(1, $filtered);
        $this->assertContains($pastEvent, $filtered);
    }

    /**
     * @return Event\EventInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createEventMock(): Event\EventInterface
    {
        return $this->createMock(Event\EventInterface::class);
    }

    /**
     * @param string $time
     *
     * @return Event\EventInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createEventSpy(string $time): Event\EventInterface
    {
        $event = $this->createEventMock();

        $event
            ->expects($this->atLeastOnce())
            ->method('time')
            ->willReturn($time);

        return $event;
    }
}
