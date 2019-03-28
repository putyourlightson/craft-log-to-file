# Log To File Extension for Craft CMS 3

The Log To File extension provides a simple way for logging messages to a specific file. It is intended to be used a helper component for other Craft modules and plugins.

## Requirements

This component requires Craft CMS 3.0.0 or later.

## Usage

Install it manually using composer or add it as a dependency to your plugin.
```
composer require putyourlightson/craft-log-to-file
```    
Then you can write messages to a log file as follows.

```php

use putyourlightson\logtofile\LogToFile;

// ...

$message = 'The message to log.';

LogToFile::info($message);
```

## License

This module is licensed for free under the MIT License.

<small>Created by [PutYourLightsOn](https://putyourlightson.com/).</small>