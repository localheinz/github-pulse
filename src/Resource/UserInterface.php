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

namespace Localheinz\GitHub\Pulse\Resource;

/**
 * @see https://developer.github.com/v3/users/#get-a-single-user
 */
interface UserInterface
{
    public function login(): string;
}
