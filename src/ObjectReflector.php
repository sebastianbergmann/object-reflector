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

use function is_string;
use function strrpos;
use function substr;

final class ObjectReflector
{
    /**
     * Maps mangled property names to their unmangled representation, per class.
     *
     * Only mangled names (those of declared private and protected properties)
     * are cached; dynamic property names are never mangled and are used as-is.
     *
     * @var array<class-string, array<non-empty-string, string>>
     */
    private static array $names = [];

    /**
     * @return array<array-key, mixed>
     */
    public function getProperties(object $object): array
    {
        $properties  = [];
        $className   = $object::class;
        $cachedNames = self::$names[$className] ?? [];

        foreach ((array) $object as $name => $value) {
            /*
             * Names of public (and dynamic) properties are not mangled and
             * therefore need no processing at all.
             */
            if (!is_string($name) || $name === '' || $name[0] !== "\0") {
                $properties[$name] = $value;

                continue;
            }

            if (isset($cachedNames[$name])) {
                $properties[$cachedNames[$name]] = $value;

                continue;
            }

            /*
             * The name of a private property is mangled to "\0Class\0property",
             * that of a protected property to "\0*\0property". The name of an
             * anonymous class contains a null byte itself, so the separator has
             * to be searched for from the end of the string.
             */
            $separator = strrpos($name, "\0");

            if ($separator === false || $separator < 1) {
                $properties[$name] = $value;

                continue;
            }

            $declaringClass = substr($name, 1, $separator - 1);
            $propertyName   = substr($name, $separator + 1);

            $unmangledName = $declaringClass === $className ? $propertyName : $declaringClass . '::' . $propertyName;

            $cachedNames[$name]      = $unmangledName;
            self::$names[$className] = $cachedNames;

            $properties[$unmangledName] = $value;
        }

        return $properties;
    }
}
