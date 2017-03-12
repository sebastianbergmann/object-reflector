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

class ObjectReflector
{
    /**
     * @param object $object
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function getAttributes($object): array
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException;
        }

        $attributes = [];
        $reflector  = new \ReflectionObject($object);

        foreach ($reflector->getProperties() as $attribute) {
            $attribute->setAccessible(true);

            try {
                $attributes[$attribute->getName()] = $attribute->getValue($object);
            } catch (\Throwable $t) {
                continue;
            }
        }

        while ($reflector = $reflector->getParentClass()) {
            foreach ($reflector->getProperties() as $attribute) {
                $attribute->setAccessible(true);

                try {
                    $attributes[$attribute->getDeclaringClass()->getName() . '::' . $attribute->getName()] = $attribute->getValue($object);
                } catch (\Throwable $t) {
                    continue;
                }
            }
        }

        return $attributes;
    }
}
