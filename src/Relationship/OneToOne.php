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

declare(strict_types=1);

namespace Hector\Orm\Relationship;

class OneToOne extends ManyToOne
{
    /**
     * @inheritDoc
     */
    public function reverse(string $name): Relationship
    {
        return new OneToOne(
            $name,
            $this->getTargetEntity(),
            $this->getSourceEntity(),
            array_combine($this->getTargetColumns(), $this->getSourceColumns())
        );
    }
}