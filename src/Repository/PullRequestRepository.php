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

namespace Localheinz\GitHub\Pulse\Repository;

use Localheinz\GitHub\Pulse\Resource;

final class PullRequestRepository extends AbstractRepository implements PullRequestRepositoryInterface
{
    public function find(
        Resource\OrganizationInterface $organization,
        Resource\RepositoryInterface $repository,
        int $id
    ): Resource\PullRequestInterface {
        $path = $this->resourceFilePath(
            $organization,
            $repository,
            $id
        );

        $data = $this->fromFile($path);

        if (empty($data)) {
            $data = $this->resourceFromApi(
                $organization,
                $repository,
                $id
            );
        }

        $this->toFile(
            $path,
            $data
        );

        return $this->denormalize($data);
    }

    public function findAll(Resource\OrganizationInterface $organization, Resource\RepositoryInterface $repository): array
    {
        $page = 1;

        $collectionData = [];
        $previousData = [];

        while (true) {
            $path = $this->collectionFilePath(
                $organization,
                $repository,
                $page
            );

            $data = $this->fromFile($path);

            if (self::PER_PAGE > \count($data)) {
                $data = $this->collectionFromApi(
                    $organization,
                    $repository,
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

        $pullRequests = \array_map(function (array $data) {
            return $this->denormalize($data);
        }, $collectionData);

        \usort($pullRequests, function (Resource\PullRequestInterface $a, Resource\PullRequestInterface $b) {
            return $a->number() <=> $b->number();
        });

        return $pullRequests;
    }

    protected function resourceClassName(): string
    {
        return Resource\PullRequest::class;
    }

    private function resourceFilePath(
        Resource\OrganizationInterface $organization,
        Resource\RepositoryInterface $repository,
        int $id
    ): string {
        return \sprintf(
            '/repos/%s/%s/pulls/%s.json',
            $organization->login(),
            $repository->name(),
            $id
        );
    }

    private function resourceFromApi(
        Resource\OrganizationInterface $organization,
        Resource\RepositoryInterface $repository,
        int $id
    ): array {
        return $this->client()->pullRequest()->show(
            $organization->login(),
            $repository->name(),
            $id
        );
    }

    private function collectionFilePath(
        Resource\OrganizationInterface $organization,
        Resource\RepositoryInterface $repository,
        int $page
    ): string {
        return \sprintf(
            '/repos/%s/%s/pulls-%s.json',
            $organization->login(),
            $repository->name(),
            $page
        );
    }

    private function collectionFromApi(
        Resource\OrganizationInterface $organization,
        Resource\RepositoryInterface $repository,
        int $page
    ): array {
        return $this->client()->pullRequest()->all(
            $organization->login(),
            $repository->name(),
            [
                'direction' => 'asc',
                'page' => $page,
                'per_page' => self::PER_PAGE,
                'sort' => 'created',
                'state' => 'all',
            ]
        );
    }
}
