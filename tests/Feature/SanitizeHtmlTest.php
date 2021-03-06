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

class SanitizeHtmlTest extends TestCase
{
    /** @test */
    public function sanitize_html()
    {
        $html = "<p>Allo my star <iframe srcdoc=\"<img src=x onerror=alert(1)>\"></iframe>one</p>";
        $safeHTML = new SafeHTML();
        $sanitizedHTML = $safeHTML->sanitizeHTML($html);

        $this->assertEmpty($sanitizedHTML);
    }
}