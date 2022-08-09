# Log To File Helper for Craft CMS 3

The Log To File helper provides a simple way for logging messages to a specific file. It is intended to be used a helper class for modules and plugins in [Craft CMS](https://craftcms.com/).

## Craft 4 Support

This package will _not_ be updated for use with Craft 4. Instead, we recommend you use a custom Monolog log target to achieve a similar (better) result, as explained in https://putyourlightson.com/articles/adding-logging-to-craft-plugins-with-monolog

## Requirements

This component requires Craft CMS 3.0.0 or later.

## Usage

Install it manually using composer:

```shell
composer require putyourlightson/craft-log-to-file
```

Or add it as a dependency to your plugin:

```
"require": {
    "putyourlightson/craft-log-to-file": "^1.0.0"
},
```
Then you can write messages to a log file as follows.

```php
use putyourlightson\logtofile\LogToFile;

$message = 'The message to log.';

// Log as info
LogToFile::info($message, 'my-plugin-handle');

// Log as error
LogToFile::error($message, 'my-plugin-handle');

// Log as Yii message level
LogToFile::log($message, 'my-plugin-handle', 'error');

// Log as custom category
LogToFile::log($message, 'my-plugin-handle', 'custom-category');
```

The result is a concise log file that contains messages relevant to your module/plugin only.

### File: `my-plugin-handle.log`

```
2019-04-24 09:47:14 [info] Notification email sent to user #34.
2019-04-24 17:53:45 [info] Notification email sent to user #56.
2019-04-24 19:45:52 [error] Template `notification` not found.
2019-04-24 19:56:13 [debug] Template `notification` could not be rendered.
```

## License

This software is licensed for free under the MIT License.

---

Created by [PutYourLightsOn](https://putyourlightson.com/).
