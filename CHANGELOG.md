# Changelog

## [3.1.0](https://github.com/RubenJ01/php-humanize/compare/v3.0.0...v3.1.0) (2026-04-22)


### Features

* normalize locale aliases across intl formatters ([#62](https://github.com/RubenJ01/php-humanize/issues/62)) ([06a5e40](https://github.com/RubenJ01/php-humanize/commit/06a5e409243796f3f9ff6a0d78d5f0b8b7c6129d))

## [3.0.0](https://github.com/RubenJ01/php-humanize/compare/v2.4.0...v3.0.0) (2026-03-31)


### ⚠ BREAKING CHANGES

* migrate v3 to intl-only locale strategy ([#52](https://github.com/RubenJ01/php-humanize/issues/52))

### Features

* migrate v3 to intl-only locale strategy ([#52](https://github.com/RubenJ01/php-humanize/issues/52)) ([1a7764b](https://github.com/RubenJ01/php-humanize/commit/1a7764b2b61ee95b559071fbfdfbcf1cf176422c))

## [2.4.0](https://github.com/RubenJ01/php-humanize/compare/v2.3.0...v2.4.0) (2026-03-31)


### Features

* add HumanizerFactory for safer formatter configuration ([#49](https://github.com/RubenJ01/php-humanize/issues/49)) ([64afe07](https://github.com/RubenJ01/php-humanize/commit/64afe07655d9a257b9e0fad138fdcf8b00a56793))

## [2.3.0](https://github.com/RubenJ01/php-humanize/compare/v2.2.0...v2.3.0) (2026-03-22)


### Features

* **config:** add HumanizerConfig for centralized defaults ([#45](https://github.com/RubenJ01/php-humanize/issues/45)) ([d71550d](https://github.com/RubenJ01/php-humanize/commit/d71550d3c53261ac7f8254138ec330aea82ea3b4))
* **formatters:** added a localized formatter for numbers ([#41](https://github.com/RubenJ01/php-humanize/issues/41)) ([3266967](https://github.com/RubenJ01/php-humanize/commit/32669671143ceaff2e708cd32f85af3da0a6a646))
* **formatters:** added a localized formatter for percentages ([#47](https://github.com/RubenJ01/php-humanize/issues/47)) ([a852eb0](https://github.com/RubenJ01/php-humanize/commit/a852eb0d8615f2f677edec4240d5102670922c8e))

## [2.2.0](https://github.com/RubenJ01/php-humanize/compare/v2.1.0...v2.2.0) (2026-03-20)


### Features

* add year and constants to date conversion formatter ([#39](https://github.com/RubenJ01/php-humanize/issues/39)) ([927d79a](https://github.com/RubenJ01/php-humanize/commit/927d79aebb942fd12504ae089cabeaacc4426c73))

## [2.1.0](https://github.com/RubenJ01/php-humanize/compare/v2.0.1...v2.1.0) (2026-03-20)


### Features

* implemented a formatter for transforming dates ([#37](https://github.com/RubenJ01/php-humanize/issues/37)) ([7902e28](https://github.com/RubenJ01/php-humanize/commit/7902e28dd186f65651e87a246ecc1f9292c0ed0f))

## [2.0.1](https://github.com/RubenJ01/php-humanize/compare/v2.0.0...v2.0.1) (2026-03-19)


### Bug Fixes

* also run the ci.yml pipeline on pr creation to main ([#36](https://github.com/RubenJ01/php-humanize/issues/36)) ([02dae2e](https://github.com/RubenJ01/php-humanize/commit/02dae2ea8a01b9431d65c749370358eb15193e15))
* ci pipeline ([#34](https://github.com/RubenJ01/php-humanize/issues/34)) ([b8f3a9e](https://github.com/RubenJ01/php-humanize/commit/b8f3a9eb8b8277f01d31735ed16a33c995a75e16))

## [2.0.0](https://github.com/RubenJ01/php-humanize/compare/v1.2.0...v2.0.0) (2026-03-19)


### ⚠ BREAKING CHANGES

* add dynamic formatter registry and runtime formatter registration ([#31](https://github.com/RubenJ01/php-humanize/issues/31))

### Features

* add dynamic formatter registry and runtime formatter registration ([#31](https://github.com/RubenJ01/php-humanize/issues/31)) ([e303ae3](https://github.com/RubenJ01/php-humanize/commit/e303ae34729872a8234dbf87b27d94c769d2f073))

## [1.2.0](https://github.com/RubenJ01/php-humanize/compare/v1.1.1...v1.2.0) (2026-03-18)


### Features

* **formatters:** add human-readable data rate formatting ([#24](https://github.com/RubenJ01/php-humanize/issues/24)) ([089480f](https://github.com/RubenJ01/php-humanize/commit/089480fad87dbcd07204c8f831a3836914f9032c))
* implement a text truncation method ([#30](https://github.com/RubenJ01/php-humanize/issues/30)) ([571bced](https://github.com/RubenJ01/php-humanize/commit/571bcedb004f453b57ed20a9856ca56354980c7c))


### Bug Fixes

* formatting in CHANGELOG.md ([a671caf](https://github.com/RubenJ01/php-humanize/commit/a671caf5680698822b45115cd1e187c4892fdb80))

## [1.1.1](https://github.com/RubenJ01/php-humanize/compare/v1.1.0...v1.1.1) (2026-03-17)


### Bug Fixes

* faulty woodpecker pipeline ([#19](https://github.com/RubenJ01/php-humanize/issues/19)) ([0961be9](https://github.com/RubenJ01/php-humanize/commit/0961be9ed8ee9d9121cf41f5c1729c992bb47fe6))
* **formatters:** harden NumberToWordsFormatter and FileSizeFormatter mutation boundaries ([#22](https://github.com/RubenJ01/php-humanize/issues/22)) ([49af56c](https://github.com/RubenJ01/php-humanize/commit/49af56c83582fdfbd1f873d2d5c847ba485932c7))
* **formatters:** kill all escaped mutatants ([#21](https://github.com/RubenJ01/php-humanize/issues/21)) ([40b0ad1](https://github.com/RubenJ01/php-humanize/commit/40b0ad1365e419b533cad0393c495cd957c6e46f))

## [1.1.0](https://github.com/RubenJ01/php-humanize/compare/v1.0.0...v1.1.0) (2026-03-14)


### Features

* add duration formatting ([#14](https://github.com/RubenJ01/php-humanize/issues/14)) ([e9f214f](https://github.com/RubenJ01/php-humanize/commit/e9f214fcff3ec332347a407693ecefc73250b5a1))
* add number to words conversion ([#11](https://github.com/RubenJ01/php-humanize/issues/11)) ([87c32c7](https://github.com/RubenJ01/php-humanize/commit/87c32c763f849e23bafd302549c14d041f2bc7f1))
* add quantity pluralization ([#8](https://github.com/RubenJ01/php-humanize/issues/8)) ([53e8937](https://github.com/RubenJ01/php-humanize/commit/53e8937d9c3b1bc2cfc32c18c5a4427f1286a025))

## 1.0.0 (2026-03-14)


### Features

* initial project setup with core humanizer methods ([2016a32](https://github.com/RubenJ01/php-humanize/commit/2016a3268494cf6d50301b3ed2d00c13993ba6db))
