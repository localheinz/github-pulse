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

use Github\Client;
use League\Flysystem;
use Symfony\Component\Serializer;

abstract class AbstractRepository
{
    protected const PER_PAGE = 30;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Flysystem\FilesystemInterface
     */
    private $filesystem;

    /**
     * @var Serializer\Normalizer\DenormalizerInterface
     */
    private $denormalizer;

    final public function __construct(
        Client $client,
        Serializer\Normalizer\DenormalizerInterface $denormalizer,
        Flysystem\FilesystemInterface $filesystem
    ) {
        $this->client = $client;
        $this->filesystem = $filesystem;
        $this->denormalizer = $denormalizer;
    }

    abstract protected function resourceClassName(): string;

    final protected function fromFile(string $path): array
    {
        if (false === $this->filesystem->has($path)) {
            return [];
        }

        $content = $this->filesystem->read($path);

        if (false === $content) {
            return [];
        }

        $data = \json_decode(
            $content,
            true
        );

        if (!\is_array($data)) {
            $this->filesystem->delete($path);

            return [];
        }

        return $data;
    }

    final protected function toFile(string $path, array $data): void
    {
        if ($this->filesystem->has($path)) {
            $this->filesystem->delete($path);
        }

        $content = \json_encode(
            $data,
            \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES
        );

        if (false === $content) {
            return;
        }

        $this->filesystem->write(
            $path,
            $content
        );
    }

    final protected function client(): Client
    {
        return $this->client;
    }

    final protected function denormalize(array $data)
    {
        return $this->denormalizer->denormalize(
            $data,
            $this->resourceClassName()
        );
    }
}
