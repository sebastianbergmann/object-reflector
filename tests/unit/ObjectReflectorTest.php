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
use ReflectionClass;
use SebastianBergmann\ObjectReflector\TestFixture\ChildClass;
use SebastianBergmann\ObjectReflector\TestFixture\ChildClassRedeclaringPrivateProperty;
use SebastianBergmann\ObjectReflector\TestFixture\ChildClassWithNonPrivateProperties;
use SebastianBergmann\ObjectReflector\TestFixture\ClassThatCreatesAnonymousClass;
use SebastianBergmann\ObjectReflector\TestFixture\ClassWithIntegerPropertyName;
use SebastianBergmann\ObjectReflector\TestFixture\ClassWithPropertyHooks;
use SebastianBergmann\ObjectReflector\TestFixture\ClassWithUninitializedProperty;
use SebastianBergmann\ObjectReflector\TestFixture\ParentClassWithNonPrivateProperties;
use SebastianBergmann\ObjectReflector\TestFixture\ParentClassWithPrivateProperty;
use stdClass;

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

    public function testReflectsProtectedAndPublicPropertiesOfObject(): void
    {
        $o = new ChildClassWithNonPrivateProperties;

        $this->assertSame(
            [
                'publicInParent'       => 'public',
                '*::protectedInParent' => 'protected',
                'publicInChild'        => 'public',
                '*::protectedInChild'  => 'protected',
                'privateInChild'       => 'private',
            ],
            $this->objectReflector->getProperties($o),
        );
    }

    public function testReflectsPrivatePropertyThatIsRedeclaredInChildClass(): void
    {
        $o = new ChildClassRedeclaringPrivateProperty;

        $this->assertSame(
            [
                ParentClassWithPrivateProperty::class . '::property' => 'parent',
                'property'                                           => 'child',
            ],
            $this->objectReflector->getProperties($o),
        );
    }

    public function testReflectsPropertiesOfObjectOfAnonymousClass(): void
    {
        $o = ClassThatCreatesAnonymousClass::create();

        $this->assertSame(
            [
                'publicProperty'       => 'public',
                '*::protectedProperty' => 'protected',
                'privateProperty'      => 'private',
            ],
            $this->objectReflector->getProperties($o),
        );
    }

    public function testDoesNotReflectUninitializedProperty(): void
    {
        $o = new ClassWithUninitializedProperty;

        $this->assertSame(
            [
                'initialized' => 'value',
            ],
            $this->objectReflector->getProperties($o),
        );
    }

    /**
     * Virtual properties have no backing storage and therefore nothing to reflect.
     * Backed properties are reflected using the value that is stored in them; their
     * get hooks are not invoked. This distinguishes the reflection performed here
     * from get_object_vars() and ReflectionProperty::getValue(), both of which do
     * invoke get hooks.
     */
    public function testReflectsValueStoredInBackedPropertyWithHooksAndIgnoresVirtualProperty(): void
    {
        $o = new ClassWithPropertyHooks;

        $this->assertSame(
            [
                'backed'             => 'public',
                '*::protectedBacked' => 'protected',
                'privateBacked'      => 'private',
            ],
            $this->objectReflector->getProperties($o),
        );
    }

    public function testReflectsPropertyWhoseNameContainsNullByte(): void
    {
        $o = (object) ["a\0b" => 'value'];

        $this->assertSame(
            [
                "a\0b" => 'value',
            ],
            $this->objectReflector->getProperties($o),
        );
    }

    public function testReflectsObjectWithoutProperties(): void
    {
        $this->assertSame([], $this->objectReflector->getProperties(new stdClass));
    }

    /**
     * Reflecting an object must not have side effects. Unlike get_object_vars(),
     * ReflectionProperty::getValue(), and iteration, reflection performed here does
     * not trigger the initializer of a lazy object. The properties of a lazy object
     * that has not been initialized yet are not initialized, though, and are
     * therefore not reflected.
     */
    public function testDoesNotInitializeLazyObject(): void
    {
        $initialized = false;

        $class = new ReflectionClass(ParentClassWithNonPrivateProperties::class);

        $object = $class->newLazyGhost(
            static function (ParentClassWithNonPrivateProperties $object) use (&$initialized): void
            {
                $initialized = true;

                $object->publicInParent = 'initialized';
            },
        );

        $this->assertSame([], $this->objectReflector->getProperties($object));
        $this->assertFalse($initialized);
        $this->assertTrue($class->isUninitializedLazyObject($object));
    }

    public function testReflectsPropertiesOfInitializedLazyObject(): void
    {
        $class = new ReflectionClass(ParentClassWithNonPrivateProperties::class);

        $object = $class->newLazyGhost(
            static function (ParentClassWithNonPrivateProperties $object): void
            {
                $object->publicInParent = 'initialized';
            },
        );

        $class->initializeLazyObject($object);

        $this->assertSame(
            [
                'publicInParent'       => 'initialized',
                '*::protectedInParent' => 'protected',
            ],
            $this->objectReflector->getProperties($object),
        );
    }
}
