<?php

namespace BADDIServices\SafeHTML;

class SafeHTML
{
    /** @var array */
    private $notAllowedTags = ["script", "meta", "frameset", "applet", "object", "frameset"];

    /** @var array */
    private $notAllowedEmptyTags = ["script", "meta", "frameset", "applet", "object", "frameset"];

    /** @var array */
    private $notAllowedAttrs = ["script", "meta", "frameset", "applet", "object", "frameset"];

    public function sanitize(string $value): string
    {
        $safeValue = filter_var($value, FILTER_SANITIZE_STRING);
        if(!$safeValue) {
            return '';
        }

        return $safeValue;
    }

    public function sanitizeArray(array $values): array
    {
        $safeValues = filter_var_array($values, FILTER_SANITIZE_STRING);
        if(!$safeValues) {
            return [];
        }

        return $safeValues;
    }

    public function encodeEntities(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
    }
    
    public function decodeEntities($value): string
    {
        return html_entity_decode($value, ENT_QUOTES, "UTF-8");
    }

    public function sanitizeHTML(string $value): string
    {
        $valid = preg_match('%^(<\s*)(/\s*)?([a-zA-Z0-9]+\s*)([^>]*)(>?)$%', $value, $matches);
        if (!strlen($value) || $valid !== 1) {
            return '';
        }
      
        $doc = new \DOMDocument("1.0", "UTF-8");
        libxml_use_internal_errors(false);

        $this->removeNullCharacter($value);

        // Remove Netscape 4 JS entities
        $value = preg_replace('%&\\s*\\{[^}]*(\\}\\s*;?|$)%', '', $value);

        $this->removeSpacing($value);

        $html = mb_convert_encoding("<html>${value}</html>", "HTML-ENTITIES", "UTF-8");

        if ($doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS)) {
            foreach ($doc->getElementsByTagName('*') as $tag) {
                if (in_array(strtolower($tag->tagName), $this->notAllowedTags) || $tag->nodeType === XML_CDATA_SECTION_NODE) {
                    $tag->parentNode->removeChild($tag);

                    continue;
                }

                foreach ($tag->attributes as $attr) {
                    if (in_array(strtolower($attr->nodeName), $this->notAllowedAttrs) || $attr->nodeType === XML_ATTRIBUTE_CDATA) {
                        $tag->removeAttribute($attr->nodeName);
                    }
                }

                if (in_array(strtolower($tag->tagName), $this->notAllowedEmptyTags) && $tag->attributes->count() === 0) {
                    $tag->parentNode->removeChild($tag);
                }
            }
        }

        $safeHTML = $doc->saveHTML($doc->getElementsByTagName('html')->item(0));
        if (!$safeHTML) {
            return '';
        }

        $safeHTML = substr($safeHTML, 6, -7);

        return $this->encodeEntities($safeHTML);
    }

    private function removeSpacing(string &$value): string
    {
        return preg_replace("(?:\s|\"|'|\+|&#x0[9A-F];|%0[9a-f])*?", '', $value);
    }
    
    private function removeNullCharacter(string &$value): string
    {
        return preg_replace(chr(0), '', $value);
    }
}