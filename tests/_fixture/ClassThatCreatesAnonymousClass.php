<?php declare(strict_types=1);
/*
 * This file is part of sebastian/object-reflector.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\ObjectReflector\TestFixture;

final class ClassThatCreatesAnonymousClass
{
    public static function create(): object
    {
        return new class
        {
            public string $publicProperty       = 'public';
            protected string $protectedProperty = 'protected';
            private string $privateProperty     = 'private';
        };
    }
}
