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

namespace Localheinz\GitHub\Pulse\Repository;

use Localheinz\GitHub\Activity\Pulse;
use Localheinz\GitHub\Pulse\Resource;

interface OrganizationRepositoryInterface
{
    /**
     * @param string $name
     *
     * @throws Pulse\ResourceNotFoundException
     * @throws \InvalidArgumentException
     *
     * @return Resource\OrganizationInterface
     */
    public function find(string $name): Resource\OrganizationInterface;
}
