# Log To File Helper Changelog

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
