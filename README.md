# Safe HTML

[![Licence](https://img.shields.io/github/license/baddiservices/safe-html?logo=MIT)](./LICENSE)
![PHP Version](https://img.shields.io/packagist/php-v/baddiservices/safehtml)
[![Open issues](https://img.shields.io/github/issues-raw/baddiservices/safe-html)](https://github.com/baddiservices/safe-html/issues?q=is%3Aissue+is%3Aopen)
[![Stars](https://img.shields.io/github/stars/baddiservices/safe-html)](https://github.com/baddiservices/safe-html/stargazers)
[![Downloads](https://img.shields.io/packagist/dm/baddiservices/safehtml)](https://packagist.org/packages/baddiservices/safehtml)
[![Twitter Follow](https://img.shields.io/twitter/follow/5baddi?style=social)](https://twitter.com/intent/follow?screen_name=5baddi)

Safe HTML package help to prevent XSS vulnerability via HTML content.

---

Installation
------------

Use [Composer](https://getcomposer.org/) to install the package:

```
$ composer require baddiservices/safehtml
```

Examples
--------

Validate the input is HTML or not
```php
...

use BADDIServices\SafeHTML\SafeHTML;

class DemoController extends Controller
{
    /** @var SafeHTML **/
    private $safeHTML;

    public function __construct(SafeHTML $safeHTML)
    {
        $this->safeHTML = $safeHTML;
    }

    public function IndexAction(Request $request)
    {
        $htmlContent = $request->input("content");
        if ($this->validate($htmlContent)) {
            // TODO: is valid HTML continue the process
        }
    }
}
```

Prevent XSS from HTML
```php
...

$sanitizedHTML = $safeHTML->sanitizeHTML($content);
```

Prevent XSS from text
```php
...

$sanitizedText = $safeHTML->sanitize($text);
```

Prevent XSS from link
```php
...

$sanitizedURL = $safeHTML->sanitizeURL($url);
```

Available methods
-----------------

Method                                                   | Description
-------------------------------------------------------- | --------------------------------------------------
`validate($value)`                                       | Verify text is HTML
`sanitize($value)`                                       | Sanitize text to prevent HTML tags
`sanitizeAll($values)`                                   | Sanitize array of texts to prevent HTML tags
`sanitizeHTML($value)`                                   | Sanitize HTML to prevent XSS vulnerability
`encodeEntities($value)`                                 | Encode special characters to HTML entities
`decodeEntities($value)`                                 | Decode HTML entities to their corresponding characters
`setBlackListPath($blackListPath)`                                 | Set a custom path of the blacklist json file
`getEncoding()`                                 | Get characters encoding
`setEncoding($encodage)`                                 | Set characters encoding

Blacklist file example
-----------------------
You can check the [blacklist](./src/blacklist.json) used by default
```json
{
    "tags": {
        "not-allowed": [],
        "not-allowed-empty": []
    },
    "attributes": {
        "not-allowed": []
    }
}
```

Exceptions
-----------------

Name                                                                                     | Code        | Description
---------------------------------------------------------------------------------------- | ----------- | ----------------------------------------
[BlackListNotLoadedException](./src/Exceptions/BlackListNotLoadedException.php)          | 11          | Failed to load blacklist file

Contribute
----------

Contributions to the package are always welcome!

* Report any bugs or issues you find.
* Clone the code source and  submit your pull request.