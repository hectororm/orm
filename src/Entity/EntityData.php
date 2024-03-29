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

namespace Hector\Orm\Entity;

final class EntityData
{
    private Related $related;
    private ?PivotData $pivot = null;
    private array $data = [];

    public function __construct(private Entity $entity)
    {
        $this->related = new Related($this->entity);
    }

    public function __serialize(): array
    {
        return [
            'related' => $this->related,
            'pivot' => $this->pivot,
            'data' => $this->data,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->related = $data['related'];
        $this->pivot = $data['pivot'];
        $this->data = $data['data'];
    }

    public function restore(Entity $entity): void
    {
        $this->entity = $entity;
        $this->related->restore($entity);
    }

    public function __debugInfo(): ?array
    {
        return [];
    }

    public function getRelated(): Related
    {
        return $this->related;
    }

    /**
     * Get pivot data.
     *
     * @return PivotData|null
     */
    public function getPivot(): ?PivotData
    {
        return $this->pivot;
    }

    /**
     * Set pivot data.
     *
     * @param PivotData $pivot
     */
    public function setPivot(PivotData $pivot): void
    {
        $this->pivot = $pivot;
    }

    /**
     * Unset pivot data.
     */
    public function unsetPivot(): void
    {
        $this->pivot = null;
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->data[$name] ?? $default;
    }

    public function set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    public function exists(string $name): bool
    {
        return array_key_exists($name, $this->data);
    }
}