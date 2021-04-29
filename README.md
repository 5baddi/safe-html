# Safe HTML

[![Licence](https://img.shields.io/github/license/baddiservices/safe-html?logo=MIT)](./LICENSE)
![PHP Version](https://img.shields.io/packagist/php-v/baddiservices/safehtml)
[![Open issues](https://img.shields.io/github/issues-raw/baddiservices/safe-html)](https://github.com/baddiservices/safe-html/issues?q=is%3Aissue+is%3Aopen)
[![Stars](https://img.shields.io/github/stars/baddiservices/safe-html)](https://github.com/baddiservices/safe-html/stargazers)
[![Downloads](https://img.shields.io/packagist/dm/baddiservices/safehtml)](https://packagist.org/packages/baddiservices/safehtml)
[![Tweet](https://img.shields.io/twitter/url?style=social&url=https%3A%2F%2Fgithub.com%2Fbaddiservices%2Fsafe-html)](https://twitter.com/intent/tweet?text=Wow:&url=https%3A%2F%2Fpackagist.org%2Fpackages%2Fbaddiservices%2Fsafehtml)

Safe HTML package help to prevent XSS vulnerability via HTML content

Installation
------------

Use [Composer] to install the package:

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
    private $SafeHTML;

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

Available methods
-----------------

Method                                                   | Description
-------------------------------------------------------- | --------------------------------------------------
`validate($value)`                                       | Verify text is HTML
`sanitize($value)`                                       | Sanitize text to prevent HTML tags
`sanitizeArray($values)`                                 | Sanitize array of texts to prevent HTML tags
`sanitizeHTML($value)`                                   | Sanitize HTML to prevent XSS vulnerability
`encodeEntities($value)`                                 | Encode special characters to HTML entities
`decodeEntities($value)`                                 | Decode HTML entities to their corresponding characters

Contribute
----------

Contributions to the package are always welcome!

* Report any bugs or issues you find.
* Clone the code source and publish pull your request.