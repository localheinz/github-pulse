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

namespace Localheinz\GitHub\Pulse\Test\Unit\Exception;

use Ergebnis\Test\Util\Helper;
use Localheinz\GitHub\Pulse\Exception;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Localheinz\GitHub\Pulse\Exception\ResourceNotFoundException
 */
final class ResourceNotFoundExceptionTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsExceptionInterface(): void
    {
        self::assertClassImplementsInterface(
            Exception\ExceptionInterface::class,
            Exception\ResourceNotFoundException::class
        );
    }

    public function testExtendsRuntimeException(): void
    {
        self::assertClassExtends(
            \RuntimeException::class,
            Exception\ResourceNotFoundException::class
        );
    }
}
