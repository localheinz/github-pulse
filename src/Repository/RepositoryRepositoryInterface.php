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

namespace Localheinz\GitHub\Pulse\Repository;

use Localheinz\GitHub\Pulse\Resource;

interface RepositoryRepositoryInterface
{
    /**
     * @param Resource\OrganizationInterface $organization
     *
     * @return Resource\RepositoryInterface[]
     */
    public function findAll(Resource\OrganizationInterface $organization): array;
}
