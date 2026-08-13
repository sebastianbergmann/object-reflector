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

use function strtoupper;

class ClassWithPropertyHooks
{
    public string $backed = 'public' {
        get => strtoupper($this->backed);
    }
    public string $virtual {
        get => 'computed';
    }
    protected string $protectedBacked = 'protected' {
        get => strtoupper($this->protectedBacked);
    }
    private string $privateBacked = 'private' {
        get => strtoupper($this->privateBacked);
    }
    private string $privateVirtual {
        get => 'computed';
    }
}
