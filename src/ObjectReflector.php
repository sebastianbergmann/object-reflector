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

use function explode;
use function is_string;

final class ObjectReflector
{
    /**
     * @return array<array-key, mixed>
     */
    public function getProperties(object $object): array
    {
        $properties = [];
        $className  = $object::class;

        foreach ((array) $object as $name => $value) {
            /*
             * Names of public (and dynamic) properties are not mangled and
             * therefore need no processing at all.
             */
            if (!is_string($name) || $name === '' || $name[0] !== "\0") {
                $properties[$name] = $value;

                continue;
            }

            $parts = explode("\0", $name);

            $properties[$parts[1] !== $className ? $parts[1] . '::' . $parts[2] : $parts[2]] = $value;
        }

        return $properties;
    }
}
