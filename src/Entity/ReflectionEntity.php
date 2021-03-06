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

use Hector\Orm\Assert\EntityAssert;
use Hector\Orm\Attributes;
use Hector\Orm\Collection\Collection;
use Hector\Orm\Exception\OrmException;
use Hector\Orm\Mapper\Mapper;
use Hector\Orm\Orm;
use Hector\Orm\Storage\EntityStorage;
use Hector\Schema\Exception\NotFoundException;
use Hector\Schema\Exception\SchemaException;
use Hector\Schema\Table;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

/**
 * Class ReflectionEntity.
 *
 * @property-read string|Entity $class Class of entity
 * @property-read string $mapper Mapper class of entity
 * @property-read string $collection Collection class of entity
 * @property-read string $table Table name of entity
 * @property-read string|null $schema Schema name of entity
 * @property-read string|null $connection Connection name of entity
 * @property-read array $types Type of columns
 * @property-read array $hidden Hidden columns
 */
class ReflectionEntity
{
    use EntityAssert;

    private string $mapper;
    private string $collection;
    private string $table;
    private ?string $schema;
    private ?string $connection;
    private array $types = [];
    private array $hidden = [];

    private ?Table $tableInstance = null;
    private Mapper $mapperInstance;
    private ReflectionClass $reflection;

    /**
     * EntityProperties constructor.
     *
     * @param string $entity
     *
     * @throws OrmException
     */
    public function __construct(protected string $entity)
    {
        $this->assertEntity($this->entity);

        $this->mapper = $this->retrieveMapper();
        $this->collection = $this->retrieveCollection();
        list(
            'table' => $this->table,
            'schema' => $this->schema,
            'connection' => $this->connection
            ) = $this->retrieveTable();
        $this->types = $this->retrieveTypes();
        $this->hidden = $this->retrieveHidden();
    }

    /**
     * PHP serialize method.
     *
     * @return array
     */
    public function __serialize(): array
    {
        return [
            'entity' => $this->entity,
            'mapper' => $this->mapper,
            'collection' => $this->collection,
            'table' => $this->table,
            'schema' => $this->schema,
            'connection' => $this->connection,
            'types' => $this->types,
            'hidden' => $this->hidden,
        ];
    }

    /**
     * PHP unserialize method.
     *
     * @param array $data
     *
     * @throws OrmException
     */
    public function __unserialize(array $data): void
    {
        $this->entity = $data['entity'] ?? throw new OrmException('Unable to unserialize ReflectionEntity');
        $this->mapper = $data['mapper'] ?? throw new OrmException('Unable to unserialize ReflectionEntity');
        $this->collection = $data['collection'] ?? throw new OrmException('Unable to unserialize ReflectionEntity');
        $this->table = $data['table'] ?? throw new OrmException('Unable to unserialize ReflectionEntity');
        $this->schema = $data['schema'] ?? null;
        $this->connection = $data['connection'] ?? null;
        $this->types = $data['types'] ?? [];
        $this->hidden = $data['hidden'] ?? [];
    }

    ////////////////
    /// RETRIEVE ///
    ////////////////

    /**
     * Retrieve types of columns.
     *
     * @return array
     * @throws OrmException
     */
    protected function retrieveTypes(): array
    {
        $types = [];
        $reflectionClass = $this->getClass();

        do {
            $attributes = $reflectionClass->getAttributes(
                Attributes\Type::class,
                ReflectionAttribute::IS_INSTANCEOF
            );

            foreach ($attributes as $attribute) {
                $typeAttribute = $attribute->newInstance();
                $types[$typeAttribute->column] = $typeAttribute->type;
            }
        } while ($reflectionClass = $reflectionClass->getParentClass());

        return $types;
    }

    /**
     * Retrieve hidden columns.
     *
     * @return array
     * @throws OrmException
     */
    protected function retrieveHidden(): array
    {
        $hidden = [];
        $reflectionClass = $this->getClass();

        do {
            $attributes = $reflectionClass->getAttributes(
                Attributes\Hidden::class,
                ReflectionAttribute::IS_INSTANCEOF
            );

            foreach ($attributes as $attribute) {
                $hidden = array_merge($attribute->newInstance()->columns, $hidden);
            }
        } while ($reflectionClass = $reflectionClass->getParentClass());

        return $hidden;
    }

    /**
     * Retrieve mapper class.
     *
     * @return string
     * @throws OrmException
     */
    protected function retrieveMapper(): string
    {
        $reflectionClass = $this->getClass();
        $mapper = null;

        do {
            $attributes = $reflectionClass->getAttributes(
                Attributes\Mapper::class,
                ReflectionAttribute::IS_INSTANCEOF
            );

            if (count($attributes) === 1) {
                $mapper = reset($attributes)->newInstance()->mapper;
            }
        } while (null === $mapper && $reflectionClass = $reflectionClass->getParentClass());

        if (!is_a($mapper, Mapper::class, true)) {
            throw new OrmException(
                sprintf(
                    'Defined mapper of "%s" entity must implement "%s" interface',
                    $this->entity,
                    Mapper::class
                )
            );
        }

        return (string)$mapper;
    }

    /**
     * Retrieve collection class.
     *
     * @return string
     * @throws OrmException
     */
    protected function retrieveCollection(): string
    {
        $reflectionClass = $this->getClass();
        $collection = null;

        do {
            $attributes = $reflectionClass->getAttributes(
                Attributes\Collection::class,
                ReflectionAttribute::IS_INSTANCEOF
            );

            if (count($attributes) === 1) {
                $collection = reset($attributes)->newInstance()->collection;
            }
        } while (null === $collection && $reflectionClass = $reflectionClass->getParentClass());

        if (!is_a($collection, Collection::class, true)) {
            throw new OrmException(
                sprintf(
                    'Defined collection of "%s" entity must extend "%s" class',
                    $this->entity,
                    Collection::class
                )
            );
        }

        return (string)$collection;
    }

    /**
     * Retrieve table.
     *
     * @return string[]
     * @throws OrmException
     */
    protected function retrieveTable(): array
    {
        $reflectionClass = $this->getClass();
        $tableAttribute = null;

        do {
            $attributes = $reflectionClass->getAttributes(
                Attributes\Table::class,
                ReflectionAttribute::IS_INSTANCEOF
            );

            if (count($attributes) === 1) {
                /** @var Attributes\Table $tableAttribute */
                $tableAttribute = reset($attributes)->newInstance();
            }
        } while (null === $tableAttribute && $reflectionClass = $reflectionClass->getParentClass());

        // Deduct table name with class name
        if (null === $tableAttribute) {
            $tableName = substr($this->entity, strrpos($this->entity, '\\') + 1);
            if (false !== ($dotPos = strrpos($tableName, '.'))) {
                $tableName = substr($tableName, 0, $dotPos);
            }
            $tableName = b_snake_case($tableName);

            $tableAttribute = new Attributes\Table($tableName);
        }

        return [
            'table' => $tableAttribute->table,
            'schema' => $tableAttribute->schema,
            'connection' => $tableAttribute->connection,
        ];
    }

    ///////////////
    /// GETTERS ///
    ///////////////

    /**
     * __get PHP magic method.
     *
     * @param string $name
     *
     * @return string|array
     * @throws OrmException|SchemaException
     */
    public function __get(string $name): string|array
    {
        return match ($name) {
            'class' => $this->entity,
            'mapper' => $this->mapper,
            'collection' => $this->collection,
            'table' => $this->getTable()->getName(),
            'schema' => $this->getTable()->getSchemaName(),
            'connection' => $this->getTable()->getSchema()->getConnection(),
            'hidden' => $this->hidden,
        };
    }

    /**
     * Get entity class name.
     *
     * @return string|Entity
     */
    public function getName(): string
    {
        return $this->entity;
    }

    /**
     * Get mapper class name.
     *
     * @return string
     */
    public function getMapperName(): string
    {
        return $this->mapper;
    }

    /**
     * Get collection class name.
     *
     * @return string
     */
    public function getCollectionName(): string
    {
        return $this->collection;
    }

    /**
     * Get reflection of class.
     *
     * @return ReflectionClass
     * @throws OrmException
     */
    public function getClass(): ReflectionClass
    {
        try {
            return $this->reflection ?? $this->reflection = new ReflectionClass($this->entity);
        } catch (ReflectionException $e) {
            throw new OrmException(sprintf('Unable to do reflection of "%s" entity class', $this->entity), 0, $e);
        }
    }

    /**
     * Get property.
     *
     * @param string $name
     * @param string|null $class
     *
     * @return ReflectionProperty
     * @throws OrmException
     */
    public function getProperty(string $name, ?string $class = null): ReflectionProperty
    {
        try {
            if (null === $class) {
                $propertyReflection = $this->getClass()->getProperty($name);
                $propertyReflection->setAccessible(true);

                return $propertyReflection;
            }

            $propertyReflection = new ReflectionProperty($class, $name);
            $propertyReflection->setAccessible(true);

            return $propertyReflection;
        } catch (ReflectionException $e) {
            throw new OrmException(sprintf('Unable to get property "%s" of entity "%s"', $name, $this->entity), 0, $e);
        }
    }

    /**
     * Get Hector data.
     *
     * @param Entity $entity
     *
     * @return EntityData
     */
    public function getHectorData(Entity $entity): EntityData
    {
        $classReflection = new ReflectionClass(Entity::class);
        $propertyReflection = $classReflection->getProperty('_hectorData');
        $propertyReflection->setAccessible(true);

        if ($propertyReflection->isInitialized($entity)) {
            return $propertyReflection->getValue($entity);
        }

        $hectorData = new EntityData($entity);
        $propertyReflection->setValue($entity, $hectorData);

        return $hectorData;
    }

    /**
     * Get table.
     *
     * @return Table
     * @throws OrmException
     */
    public function getTable(): Table
    {
        try {
            return
                $this->tableInstance ??
                Orm::get()->getSchemaContainer()->getTable($this->table, $this->schema, $this->connection);
        } catch (NotFoundException $e) {
            throw new OrmException(
                sprintf('Table "%s" not found for entity "%s"', $this->table, $this->entity),
                0,
                $e
            );
        }
    }

    /**
     * Get mapper.
     *
     * @return Mapper
     * @throws OrmException
     */
    public function getMapper(): Mapper
    {
        return $this->mapperInstance ?? $this->mapperInstance = Orm::get()->getMapper($this->entity);
    }

    /**
     * Get hidden columns.
     *
     * @return array
     */
    public function getHidden(): array
    {
        return $this->hidden;
    }

    /**
     * Get type.
     *
     * @param string $column
     *
     * @return string|null
     */
    public function getType(string $column): ?string
    {
        if (array_key_exists($column, $this->types)) {
            return $this->types[$column];
        }

        return null;
    }

    /////////////////
    /// INSTANCES ///
    /////////////////

    public function newInstance(): Entity
    {
        return new $this->entity();
    }

    /**
     * New instance of collection.
     *
     * @param iterable $input
     *
     * @return Collection
     */
    public function newInstanceOfCollection(iterable $input = []): Collection
    {
        return new $this->collection($input, $this->entity);
    }

    /**
     * New instance of mapper.
     *
     * @param EntityStorage $storage
     *
     * @return Mapper
     */
    public function newInstanceOfMapper(EntityStorage $storage): Mapper
    {
        return new $this->mapper($this->entity, $storage);
    }
}