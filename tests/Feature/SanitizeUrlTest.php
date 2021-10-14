<?php

/**
 * Safe HTML
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */

namespace BADDIServices\SafeHTML\Tests\Feature;

use BADDIServices\SafeHTML\SafeHTML;
use BADDIServices\SafeHTML\Tests\TestCase;

class SanitizeUrlTest extends TestCase
{
    /** @test */
    public function sanitize_url()
    {
        $url = "students.washington.edu/squakmix/reflect.php?param=<script>alert('xss!');</script>";
        $safeHTML = new SafeHTML();
        $sanitizedURL = $safeHTML->sanitizeURL($url);

        $this->assertNotEmpty($sanitizedURL);
    }
}