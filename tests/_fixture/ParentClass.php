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

class ParentClass
{
    private string $privateInParent   = 'private';
    private string $protectedInParent = 'protected';
    private string $publicInParent    = 'public';
}
