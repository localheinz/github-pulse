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

use Localheinz\GitHub\Pulse\Resource;

final class OrganizationRepository extends AbstractRepository implements OrganizationRepositoryInterface
{
    public function find(string $name): Resource\OrganizationInterface
    {
        $path = $this->resourceFilePath($name);

        $data = $this->fromFile($path);

        if (empty($data)) {
            $data = $this->resourceFromApi($name);
        }

        $this->toFile(
            $path,
            $data
        );

        return $this->denormalize($data);
    }

    protected function resourceClassName(): string
    {
        return Resource\Organization::class;
    }

    private function resourceFilePath(string $name): string
    {
        return \sprintf(
            '/orgs/%s.json',
            $name
        );
    }

    private function resourceFromApi(string $name): array
    {
        return $this->client()->organization()->show($name);
    }
}
