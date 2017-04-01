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

final class Commit implements CommitInterface
{
    /**
     * @var array|null
     */
    private $parents;

    /**
     * @var string
     */
    private $sha;

    /**
     * @var string
     */
    private $url;

    public function parents(): ?array
    {
        return $this->parents;
    }

    public function sha(): string
    {
        return $this->sha;
    }

    public function url(): string
    {
        return $this->url;
    }
}
