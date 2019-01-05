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

namespace Localheinz\GitHub\Pulse\Test\Unit\Event;

use Localheinz\GitHub\Pulse\Event;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 */
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

        self::assertTrue($recorder->isSorted());
        self::assertInternalType('array', $recorder->toArray());
        self::assertEmpty($recorder->toArray());
    }

    public function testRecordRecordsEvent(): void
    {
        $event = $this->createEventMock();

        $recorder = new Event\EventRecorder();

        $recorder->record($event);

        self::assertInternalType('array', $recorder->toArray());
        self::assertCount(1, $recorder->toArray());
        self::assertContains($event, $recorder->toArray());
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

        self::assertSame(\array_values($events), $recorder->toArray());
    }

    public function testRecordEventOnEmptyRecorderRecorderSorted(): void
    {
        $event = $this->createEventMock();

        $recorder = new Event\EventRecorder();

        $recorder->record($event);

        self::assertTrue($recorder->isSorted());
    }

    public function testRecordEventOnRecorderWithEventMarksRecorderUnsorted(): void
    {
        $event = $this->createEventMock();

        $recorder = new Event\EventRecorder();

        $recorder->record($event);
        $recorder->record($event);

        self::assertFalse($recorder->isSorted());
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

        self::assertFalse($recorder->isSorted());
    }

    public function testSortDoesNotSortIfRecorderIsSorted(): void
    {
        $event = $this->createEventMock();

        $event
            ->expects(self::never())
            ->method(self::anything());

        $recorder = new Event\EventRecorder();

        $recorder->record($event);

        $recorder->sort();
    }

    public function testSortSortsEventsByTime(): void
    {
        $faker = $this->faker();

        $times = \array_map(static function () use ($faker) {
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

        self::assertSame(\array_values($sorted), $recorder->toArray());
    }

    public function testFilterIfNoEventsHaveBeenRecorded(): void
    {
        $recorder = new Event\EventRecorder();

        $filtered = $recorder->filter(static function () {
            return true;
        });

        self::assertInternalType('array', $filtered);
        self::assertEmpty($filtered);
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

        $filtered = $recorder->filter(static function (Event\EventInterface $event) use ($now) {
            return $event->time() <= $now->format('Y-m-d\TH:i:s\Z');
        });

        self::assertInternalType('array', $filtered);
        self::assertCount(1, $filtered);
        self::assertContains($pastEvent, $filtered);
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
            ->expects(self::atLeastOnce())
            ->method('time')
            ->willReturn($time);

        return $event;
    }
}
