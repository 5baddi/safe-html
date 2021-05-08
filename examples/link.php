<?php

/**
 * Safe HTML
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */

require(__DIR__ . "../src/SafeHTML.php");

use BADDIServices\SafeHTML\SafeHTML;

$url = "students.washington.edu/squakmix/reflect.php?param=<script>alert('xss!');</script>";
$safeHTML = new SafeHTML();
$sanitizedURL = $safeHTML->sanitizeURL($url);

echo $sanitizedHTML;