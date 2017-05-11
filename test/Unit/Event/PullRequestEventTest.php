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
use Localheinz\GitHub\Pulse\Resource;
use PHPUnit\Framework;
use Refinery29\Test\Util\TestHelper;

final class PullRequestEventTest extends Framework\TestCase
{
    use TestHelper;

    public function testImplementsPullRequestInterface(): void
    {
        $this->assertImplements(Event\PullRequestEventInterface::class, Event\PullRequestEvent::class);
    }

    public function testConstructorSetsPullRequest(): void
    {
        $pullRequest = $this->createPullRequestMock();

        $event = new Event\PullRequestEvent($pullRequest);

        $this->assertSame($pullRequest, $event->pullRequest());
    }

    public function testTimeReturnsTimeWhenPullRequestWasCreated(): void
    {
        $createdAt = $this->getFaker()->dateTime->format('Y-m-d\TH:i:s\Z');

        $pullRequest = $this->createPullRequestMock();

        $pullRequest
            ->expects($this->once())
            ->method('createdAt')
            ->willReturn($createdAt);

        $event = new Event\PullRequestEvent($pullRequest);

        $this->assertSame($createdAt, $event->time());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Resource\PullRequestInterface
     */
    private function createPullRequestMock(): Resource\PullRequestInterface
    {
        return $this->createMock(Resource\PullRequestInterface::class);
    }
}
