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

namespace Localheinz\GitHub\Pulse\Resource;

/**
 * @link https://developer.github.com/v3/repos/commits/
 */
interface CommitInterface
{
    public function parents(): ?array;

    public function sha(): string;

    public function url(): string;
}
