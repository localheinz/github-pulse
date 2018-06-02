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

namespace Localheinz\GitHub\Pulse\Test\Unit;

use Localheinz\GitHub\Pulse;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 */
final class ProjectCodeTest extends Framework\TestCase
{
    use Helper;

    public function testProductionClassesAreAbstractOrFinal(): void
    {
        $this->assertClassesAreAbstractOrFinal(__DIR__ . '/../../src');
    }

    public function testProductionClassesHaveTests()
    {
        $this->assertClassesHaveTests(
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

    public function testTestClassesAreAbstractOrFinal(): void
    {
        $this->assertClassesAreAbstractOrFinal(__DIR__ . '/..');
    }
}
