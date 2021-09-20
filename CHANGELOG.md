# Change Log

All notable changes to this project will be documented in this file. This project adheres
to [Semantic Versioning] (http://semver.org/). For change log format,
use [Keep a Changelog] (http://keepachangelog.com/).

## [1.0.0-beta4] - In progress

### Added

- New method `Entity::isAltered(?array $columns = null): bool`

### Changed

- Property `MagicEntity::$hectorAttributes` renamed to `MagicEntity::$_hectorAttributes`
- Optimize comparison of entity

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
