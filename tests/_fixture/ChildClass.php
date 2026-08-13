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

use AllowDynamicProperties;

#[AllowDynamicProperties]
class ChildClass extends ParentClass
{
    private string $firstPrivateInChild  = 'first';
    private string $secondPrivateInChild = 'second';
    private string $thirdPrivateInChild  = 'third';

    public function __construct()
    {
        $this->undeclared = 'undeclared';
    }
}
