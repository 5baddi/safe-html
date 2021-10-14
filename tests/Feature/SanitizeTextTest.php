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

class SanitizeTextTest extends TestCase
{
    /** @test */
    public function sanitize_text()
    {
        $text = "Hi every<iframe srcdoc=\"<img src=x onerror=alert(1)>\"></iframe>one";
        $safeHTML = new SafeHTML();
        $sanitizedText = $safeHTML->sanitize($text);

        $this->assertNotEmpty($sanitizedText);
    }
}