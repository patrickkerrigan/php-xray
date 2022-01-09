# Changelog

## [1.4.0] - 2022-01-09
### Removed
- Support for PHP 7.0 has now been removed, with PHP >=7.1 being required from this version onwards. Users who cannot use a newer version of PHP should continue to use 1.3.0, which will continue to receive security fixes for at least 12 months. Users who can use a newer version of PHP are strongly encouraged to upgrade to at least PHP 7.4

### Fixed
- Deprecation errors will no longer be triggered for the return type of `JsonSerializable::jsonSerialize()` under PHP >=8.1

## [1.3.0] - 2021-04-26
### Added
- Allow setting aws.account_id for segments & subsegments

## [1.2.2] - 2021-04-26
### Fixed
- Use `traced` only for http subsegments

## [1.2.1] - 2020-10-21
### Fixed
- Default subsegments to an empty array

## [1.2.0] - 2019-01-24
### Changed
- Refactored ```getCurrentSegment()``` to drastically reduce the cost of searching large collections of segments. This may change behaviour in applications which begin earlier segments after later segments (e.g with asynchronous workloads). This can be solved by adding segments to a parent at the point ```begin()``` is called rather than before.

## [1.1.0] - 2018-07-13
### Added
- A service's version can be recorded on traces
- A client's IP address and user agent can be recorded on traces
- User identifiers can be recorded on traces
- Annotations and metadata can be added to subsegments and traces

## [1.0.1] - 2018-06-01
### Added
- Traces larger than a single UDP packet will now be fragmented rather than dropped

## [1.0.0] - 2018-05-19
### Added
- Initial release
