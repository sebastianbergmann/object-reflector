<?php declare(strict_types=1);
/*
 * This file is part of sebastian/object-reflector.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\ObjectReflector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\ObjectReflector\TestFixture\ChildClass;
use SebastianBergmann\ObjectReflector\TestFixture\ClassWithIntegerPropertyName;

#[CoversClass(ObjectReflector::class)]
final class ObjectReflectorTest extends TestCase
{
    private ObjectReflector $objectReflector;

    protected function setUp(): void
    {
        $this->objectReflector = new ObjectReflector;
    }

    public function testReflectsAttributesOfObject(): void
    {
        $o = new ChildClass;

        $this->assertEquals(
            [
                'privateInChild'                                                               => 'private',
                'protectedInChild'                                                             => 'protected',
                'publicInChild'                                                                => 'public',
                'undeclared'                                                                   => 'undeclared',
                'SebastianBergmann\ObjectReflector\TestFixture\ParentClass::privateInParent'   => 'private',
                'SebastianBergmann\ObjectReflector\TestFixture\ParentClass::protectedInParent' => 'protected',
                'SebastianBergmann\ObjectReflector\TestFixture\ParentClass::publicInParent'    => 'public',
            ],
            $this->objectReflector->getProperties($o),
        );
    }

    public function testReflectsAttributeWithIntegerName(): void
    {
        $o = new ClassWithIntegerPropertyName;

        $this->assertEquals(
            [
                1 => 2,
            ],
            $this->objectReflector->getProperties($o),
        );
    }
}
