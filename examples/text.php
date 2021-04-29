<?php

/**
 * Safe HTML
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */

require(__DIR__ . "../src/SafeHTML.php");

use BADDIServices\SafeHTML\SafeHTML;

$text = "Hi every<iframe srcdoc=\"<img src=x onerror=alert(1)>\"></iframe>one";
$safeHTML = new SafeHTML();
$sanitizedText = $safeHTML->sanitize($text);

echo $sanitizedText;