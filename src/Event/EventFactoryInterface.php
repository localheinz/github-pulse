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

use Localheinz\GitHub\Pulse\Resource;

interface EventFactoryInterface
{
    /**
     * @param Resource\PullRequestInterface $pullRequest
     *
     * @return EventInterface[]
     */
    public function fromPullRequest(Resource\PullRequestInterface $pullRequest): array;
}
