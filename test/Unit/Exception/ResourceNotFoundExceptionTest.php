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

namespace Localheinz\GitHub\Pulse\Test\Unit\Exception;

use Localheinz\GitHub\Pulse\Exception;
use PHPUnit\Framework;
use Refinery29\Test\Util\TestHelper;

final class ResourceNotFoundExceptionTest extends Framework\TestCase
{
    use TestHelper;

    public function testImplementsExceptionInterface(): void
    {
        $this->assertImplements(Exception\ExceptionInterface::class, Exception\ResourceNotFoundException::class);
    }

    public function testExtendsRuntimeException(): void
    {
        $this->assertExtends(\RuntimeException::class, Exception\ResourceNotFoundException::class);
    }
}
