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

namespace Hector\Orm\Event;

use Hector\Orm\Entity\Entity;

abstract class EntityEvent extends AbstractEvent
{
    /**
     * EntityEvent constructor.
     *
     * @param Entity $entity
     */
    public function __construct(private Entity $entity)
    {
        parent::__construct();
    }

    /**
     * Get entity.
     *
     * @return Entity
     */
    public function getEntity(): Entity
    {
        return $this->entity;
    }
}