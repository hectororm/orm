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

use Hector\Orm\Attributes\Type;
use Hector\Orm\DataType\DateTime\DateType;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

class TypeTest extends TestCase
{
    public function testConstruct()
    {
        $attribute = new Type(DateType::class, 'column');

        $this->assertEquals(DateType::class, $attribute->type);
        $this->assertEquals('column', $attribute->column);
    }

    public function testConstructWithBadCollection()
    {
        $this->expectException(TypeError::class);

        new Type(stdClass::class, 'column');
    }
}
