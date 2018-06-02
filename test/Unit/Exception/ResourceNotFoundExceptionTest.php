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
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 */
final class ResourceNotFoundExceptionTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsExceptionInterface(): void
    {
        $this->assertClassImplementsInterface(
            Exception\ExceptionInterface::class,
            Exception\ResourceNotFoundException::class
        );
    }

    public function testExtendsRuntimeException(): void
    {
        $this->assertClassExtends(
            \RuntimeException::class,
            Exception\ResourceNotFoundException::class
        );
    }
}
