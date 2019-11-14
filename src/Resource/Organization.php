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
 * @see https://developer.github.com/v3/orgs/#list-your-organizations
 */
final class Organization implements OrganizationInterface
{
    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $reposUrl;

    public function login(): string
    {
        return $this->login;
    }
}
