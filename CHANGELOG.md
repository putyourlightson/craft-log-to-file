# Log To File Helper Changelog

## 2.0.0-alpha.1 - 2022-03-03
### Added 
- Added compatibility with Craft 4.

## 1.2.3 - 2020-08-24
### Changed 
- Database exceptions are now caught in case the DB cannot be queried for a mutex lock.

## 1.2.2 - 2020-08-22
### Fixed 
- Fixed an error that could occur when the logging during a console request ([#4](https://github.com/putyourlightson/craft-log-to-file/issues/4)).

## 1.2.1 - 2020-07-01
### Changed
- Changed the max file size to 10MB.
- Changed the max log files to 5.

## 1.2.0 - 2020-07-01
### Added
- Added log file rotation.

### Fixed
- Fixed a bug that was preventing the debug toolbar from loading when messages were logged to Craft ([#233](https://github.com/putyourlightson/craft-blitz/issues/233)).

## 1.1.0 - 2019-10-09
### Changed
- The message level is now converted to one that the Yii logger (maybe) understands.
- The `logToCraft` property is now set to `true` by default so that logs will appear in the debug toolbar.
- The `logUserIp` property is deprecated in place of Craft's `storeUserIps` general config setting.

## 1.0.1 - 2019-10-09
### Changed
- The message is now trimmed to remove whitespace and empty lines.

## 1.0.0 - 2019-04-24
- Initial release.
