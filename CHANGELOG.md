# Change Log

All notable changes to this project will be documented in this file. This project adheres
to [Semantic Versioning] (http://semver.org/). For change log format,
use [Keep a Changelog] (http://keepachangelog.com/).

## [1.0.0-beta8] - In progress

### Fixed

- ManyToOne relationship with null value, do not alter entity

## [1.0.0-beta7] - 2022-06-24

### Changed

- Bump version of package `hectororm/collection` to v1.0.0-beta3
- Bump version of package `hectororm/connection` to v1.0.0-beta5
- Bump version of package `hectororm/query` to v1.0.0-beta5
- Reuse jointure for multiple where with entity relations

## [1.0.0-beta6] - 2022-02-19

### Added

- Add PivotData object to get raw columns of pivot table
- New static method `ReflectionEntity::get()` in replacement of `Orm::getEntityReflection()`
- New method `Builder::resetEntityColumns()` and so keep default comportment of `Builder::resetColumns()`
- Use `hectororm/collection` package for collections

### Changed

- Use asserts class for collection and entity
- Use constant for pivot prefix
- `AbstractMapper::updateEntity()` use all columns if no primary declared in table
- `AbstractMapper::deleteEntity()` use all columns if no primary declared in table
- `AbstractMapper::refreshEntity()` use all columns if no primary declared in table
- `AbstractMapper::getEntityAlteration()` now return all column for new entity
- With relations, now don't save related entity if not altered
- Delete pivot relation detached from collection
- `Entity::isEqualTo()` now compare pivot data too

### Removed

- Unnecessary PhpDoc
- Remove redundant assertions
- Remove personalized collections and `Collection` attribute

### Fixed

- `AbstractMapper::updateEntity()` with primary empty values throw error
- `AbstractMapper::extractPrimaryValue()` with no primary keys throw error
- Incomplete collection from `ManyToMany::get()` method
- Remove method `Collection::updateHook(): void`

## [1.0.0-beta5] - 2021-09-21

### Added

- New method `Collection::updateHook(): void` to manipulate the collection after an update

### Changed

- `Collection::contains()` compare now primary keys

### Fixed

- Check of entity storage status during deletion
- No loading relations if got a single entity

## [1.0.0-beta4] - 2021-09-20

### Added

- New method `Entity::isAltered(string ...$column): bool`

### Changed

- Property `MagicEntity::$hectorAttributes` renamed to `MagicEntity::$_hectorAttributes`
- Optimize comparison of entity
- Improve debug info of entity with the loaded relations
- Improve debug info of collection with the entities list only

### Fixed

- Get inverse relationship in one to many relation threw exception
- Infinite loop when saving with belongs relationships

## [1.0.0-beta3] - 2021-08-27

### Added

- New method `Entity::isEqualTo()`

### Changed

- Management of the types deported in the package `hectororm/data-types`

### Fixed

- Signature of `Collection::jsonSerialize(): array`
- Signature of `MagicEntity::jsonSerialize(): array`

## [1.0.0-beta2] - 2021-07-07

### Added

- New `\Hector\Orm\Event\EntityDeleteEvent` event
- Tests of `\Hector\Orm\Assert\EntityAssert` trait

### Changed

- Explode `\Hector\Orm\DataType` namespace data types family namespaces
- Renaming of `\Hector\Orm\Orm::getEntityStorageStatus()` to `\Hector\Orm\Orm::getStatus()`
- Execute `Orm::persist()` in a transaction

### Removed

- @package attributes from PhpDoc
- Unused `\Hector\Orm\ExternalEnvironment` class
- Unused `\Hector\Orm\OrmTrait` class
- Composer JSON extension requirement, already included in PHP 8
- Method `\Hector\Orm\Orm::isAttached()`, use `\Hector\Orm\Orm::getStatus()` instead

## [1.0.0-beta1] - 2021-06-02

Initial development.
