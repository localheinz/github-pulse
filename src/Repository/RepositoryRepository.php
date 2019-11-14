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

final class RepositoryRepository extends AbstractRepository implements RepositoryRepositoryInterface
{
    public function findAll(Resource\OrganizationInterface $organization): array
    {
        $page = 1;

        $collectionData = [];
        $previousData = [];

        while (true) {
            $path = $this->collectionFilePath(
                $organization,
                $page
            );

            $data = $this->fromFile($path);

            if (self::PER_PAGE > \count($data)) {
                $data = $this->collectionFromApi(
                    $organization,
                    $page
                );
            }

            if ($data === $previousData) {
                break;
            }

            $this->toFile(
                $path,
                $data
            );

            $collectionData = \array_merge(
                $collectionData,
                $data
            );

            $previousData = $data;

            ++$page;
        }

        $repositories = \array_map(function (array $data) {
            return $this->denormalize($data);
        }, $collectionData);

        \usort($repositories, static function (Resource\RepositoryInterface $a, Resource\RepositoryInterface $b) {
            return \strcmp(
                $a->name(),
                $b->name()
            );
        });

        return $repositories;
    }

    protected function resourceClassName(): string
    {
        return Resource\Repository::class;
    }

    private function collectionFilePath(Resource\OrganizationInterface $organization, int $page): string
    {
        return \sprintf(
            '/orgs/%s/repos-%s.json',
            $organization->login(),
            $page
        );
    }

    private function collectionFromApi(Resource\OrganizationInterface $organization, int $page): array
    {
        return $this->client()->organization()->repositories(
            $organization->login(),
            'all',
            $page
        );
    }
}
