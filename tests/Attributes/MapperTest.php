<?php
/*
 * This file is part of Hector ORM.
 *
 * @license   https://opensource.org/licenses/MIT MIT License
 * @copyright 2021 Ronan GIRON
 * @author    Ronan GIRON <https://github.com/ElGigi>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code, to the root.
 */

namespace Hector\Orm\Tests\Attributes;

use Hector\Orm\Attributes\Mapper;
use Hector\Orm\Mapper\GenericMapper;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

class MapperTest extends TestCase
{
    public function testConstruct()
    {
        $attribute = new Mapper(GenericMapper::class);

        $this->assertEquals(GenericMapper::class, $attribute->mapper);
    }

    public function testConstructWithBadCollection()
    {
        $this->expectException(TypeError::class);

        new Mapper(stdClass::class);
    }
}
