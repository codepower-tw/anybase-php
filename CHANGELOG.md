# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- `.gitattributes` with line-ending normalization (LF) and Composer
  `export-ignore` rules so `composer require` only fetches the runtime
  payload (`composer.json`, `LICENSE`, `README.md`, `src/`) and skips
  tests, CI configs, lint caches, and `composer.lock`.

## [0.1.0] - 2026-05-08

### Added

- Initial release.
- `Alphabets` registry with 14 predefined alphabets: `base2`, `base8`,
  `base10`, `base16`, `base16Upper`, `base32Rfc4648`, `base32Hex`,
  `crockfordBase32`, `base36`, `base58Bitcoin`, `base58Flickr`, `base62`,
  `base64Url`, `supercellHashtag`. Crockford folds `I/L → 1`, `O → 0`.
  Supercell folds `I/1 → L`, `O → 0`, `B → 8`; both fold map and case
  sensitivity configurable.

[Unreleased]: https://github.com/codepower-tw/anybase-php/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/codepower-tw/anybase-php/releases/tag/v0.1.0
