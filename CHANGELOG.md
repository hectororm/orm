# Change Log

All notable changes to this project will be documented in this file. This project adheres
to [Semantic Versioning] (http://semver.org/). For change log format,
use [Keep a Changelog] (http://keepachangelog.com/).

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
