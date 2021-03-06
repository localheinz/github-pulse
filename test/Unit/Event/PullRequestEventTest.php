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

namespace Localheinz\GitHub\Pulse\Test\Unit\Event;

use Ergebnis\Test\Util\Helper;
use Localheinz\GitHub\Pulse\Event;
use Localheinz\GitHub\Pulse\Resource;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Localheinz\GitHub\Pulse\Event\PullRequestEvent
 */
final class PullRequestEventTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsPullRequestInterface(): void
    {
        self::assertClassImplementsInterface(
            Event\PullRequestEventInterface::class,
            Event\PullRequestEvent::class
        );
    }

    public function testConstructorSetsPullRequest(): void
    {
        $pullRequest = $this->createPullRequestMock();

        $event = new Event\PullRequestEvent($pullRequest);

        self::assertSame($pullRequest, $event->pullRequest());
    }

    public function testTimeReturnsTimeWhenPullRequestWasCreated(): void
    {
        $createdAt = self::faker()->dateTime->format('Y-m-d\TH:i:s\Z');

        $pullRequest = $this->createPullRequestMock();

        $pullRequest
            ->expects(self::once())
            ->method('createdAt')
            ->willReturn($createdAt);

        $event = new Event\PullRequestEvent($pullRequest);

        self::assertSame($createdAt, $event->time());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Resource\PullRequestInterface
     */
    private function createPullRequestMock(): Resource\PullRequestInterface
    {
        return $this->createMock(Resource\PullRequestInterface::class);
    }
}
