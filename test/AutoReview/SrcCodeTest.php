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

namespace Localheinz\GitHub\Pulse\Test\AutoReview;

use Localheinz\GitHub\Pulse;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @coversNothing
 */
final class SrcCodeTest extends Framework\TestCase
{
    use Helper;

    public function testSrcClassesHaveUnitTests(): void
    {
        self::assertClassesHaveTests(
            __DIR__ . '/../../src',
            'Localheinz\\GitHub\\Pulse\\',
            'Localheinz\\GitHub\\Pulse\\Test\\Unit\\',
            [
                Pulse\Console\GenerateCommand::class,
                Pulse\Repository\OrganizationRepository::class,
                Pulse\Repository\PullRequestRepository::class,
                Pulse\Repository\RepositoryRepository::class,
                Pulse\Resource\Organization::class,
                Pulse\Resource\PullRequest::class,
                Pulse\Resource\Repository::class,
                Pulse\Resource\User::class,
            ]
        );
    }
}
