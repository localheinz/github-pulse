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

use Localheinz\GitHub\Pulse\Resource;

final class PullRequestEvent implements PullRequestEventInterface
{
    /**
     * @var Resource\PullRequestInterface
     */
    private $pullRequest;

    public function __construct(Resource\PullRequestInterface $pullRequest)
    {
        $this->pullRequest = $pullRequest;
    }

    public function pullRequest(): Resource\PullRequestInterface
    {
        return $this->pullRequest;
    }

    public function time(): string
    {
        return $this->pullRequest->createdAt();
    }
}
