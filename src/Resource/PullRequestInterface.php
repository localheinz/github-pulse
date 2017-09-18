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

namespace Localheinz\GitHub\Pulse\Resource;

/**
 * @link https://developer.github.com/v3/pulls/#list-pull-requests
 */
interface PullRequestInterface
{
    public function commits(): ?int;

    public function closedAt(): ?string;

    public function createdAt(): string;

    public function htmlUrl(): string;

    public function id(): int;

    public function mergedAt(): ?string;

    public function number(): int;

    public function title(): string;

    public function url(): string;

    public function user(): UserInterface;
}
