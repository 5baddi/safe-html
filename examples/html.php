<?php

/**
 * Safe HTML
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */

require(__DIR__ . "../src/SafeHTML.php");

use BADDIServices\SafeHTML\SafeHTML;

$html = "<p>Allo my star <iframe srcdoc=\"<img src=x onerror=alert(1)>\"></iframe>one</p>";
$safeHTML = new SafeHTML();
$sanitizedHTML = $safeHTML->sanitizeHTML($html);

echo $sanitizedHTML;