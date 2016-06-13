# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.1.0 - 2016-06-13

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- [#1](https://github.com/zendframework/zend-servicemanager-di/pull/1) removes
  support for zend-servicemanager v2. Because this package duplicated several
  classes from zend-servicemanager v2, it caused conflicts, which could be
  observed when generating an optimized autoloader with Composer.
- [#1](https://github.com/zendframework/zend-servicemanager-di/pull/1) removes
  support for PHP 5.5, marking PHP 5.6 as the minimum supported version.

### Fixed

- Nothing.

## 1.0.2 - TBD

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.1 - 2016-06-09

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Imports a patch from [zend-mvc](https://github.com/zendframework/zend-mvc/pull/149)
  that fixes some issues with the `DiAbstractServiceFactoryFactory` due to
  incorrect variable names and import statements.

## 1.0.0 - 2016-04-06

First stable release.

This package replaces the zend-servicemanager <-> zend-di integration originally
found in zend-servicemanager and zend-mvc in the v2 releases.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
