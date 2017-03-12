<?php
/*
 * This file is part of object-reflector.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SebastianBergmann\ObjectReflector;

use PHPUnit\Framework\TestCase;
use SebastianBergmann\ObjectReflector\TestFixture\ChildClass;

/**
 * @covers SebastianBergmann\ObjectReflector\ObjectReflector
 */
class ObjectReflectorTest extends TestCase
{
    /**
     * @var ObjectReflector
     */
    private $objectReflector;

    protected function setUp()/*: void */
    {
        $this->objectReflector = new ObjectReflector;
    }

    public function testReflectsAttributesOfObject()/*: void */
    {
        $o = new ChildClass;

        $this->assertEquals(
            [
                'foo'                                                            => 'baz',
                'SebastianBergmann\ObjectReflector\TestFixture\ParentClass::foo' => 'bar'
            ],
            $this->objectReflector->getAttributes($o)
        );
    }
}
