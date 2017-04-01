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

final class PullRequest implements PullRequestInterface
{
    /**
     * @var int|null
     */
    private $commits;

    /**
     * @var string|null
     */
    private $closedAt;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $htmlUrl;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string|null
     */
    private $mergedAt;

    /**
     * @var int
     */
    private $number;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $url;

    /**
     * @var User
     */
    private $user;

    public function commits(): ?int
    {
        return $this->commits;
    }

    public function closedAt(): ?string
    {
        return $this->closedAt;
    }

    public function createdAt(): string
    {
        return $this->createdAt;
    }

    public function htmlUrl(): string
    {
        return $this->htmlUrl;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function mergedAt(): ?string
    {
        return $this->mergedAt;
    }

    public function number(): int
    {
        return $this->number;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function user(): UserInterface
    {
        return $this->user;
    }
}
