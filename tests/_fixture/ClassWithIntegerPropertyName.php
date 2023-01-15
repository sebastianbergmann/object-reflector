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
class ClassWithIntegerPropertyName
{
    public function __construct()
    {
        $i          = 1;
        $this->{$i} = 2;
    }
}
