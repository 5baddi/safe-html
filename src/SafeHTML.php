<?php

/**
 * Safe HTML
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */

namespace BADDIServices\SafeHTML;

use BADDIServices\SafeHTML\Exceptions\BlackListNotLoadedException;
use DOMDocument;
use Throwable;

class SafeHTML
{
    /** @var string */
    const DEFAULT_BLACKLIST_PATH = __DIR__ . "blacklist.json";

    /** @var array */
    const NOT_ALLOWED_TAGS = ["script", "meta", "frameset", "applet", "object", "frameset"];

    /** @var array */
    const NOT_ALLOWED_EMPTY_TAGS = ["script", "meta", "frameset", "applet", "object", "frameset"];

    /** @var array */
    const NOT_ALLOWED_ATTRS = ["script", "meta", "frameset", "applet", "object", "frameset"];

    /** @var string */
    const UTF8_ENCODAGE = "UTF-8";

    /** @var array */
    private $notAllowedTags;

    /** @var array */
    private $notAllowedEmptyTags;

    /** @var array */
    private $notAllowedAttrs;

    /** @var string */
    private $encoding = self::UTF8_ENCODAGE;
    
    /** @var string */
    private $blackListPath = self::DEFAULT_BLACKLIST_PATH;

    public function __construct()
    {
        $this->loadBlackList();
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function setEncoding(string $encoding): self
    {
        $this->encoding = $encoding;

        return $this;
    }

    public function setBlackListPath(string $blackListPath): void
    {
        if (is_file($blackListPath)) {
            $this->blackListPath = $blackListPath;

            $this->loadBlackList();
        }
    }

    public function validate(string $value): bool
    {
        $valid = preg_match('%^(<\s*)(/\s*)?([a-zA-Z0-9]+\s*)([^>]*)(>?)$%', $value, $matches);

        return sizeof($matches) > 1 && $valid !== false;
    }

    public function encodeEntities(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, $this->encoding);
    }
    
    public function decodeEntities(string $value): string
    {
        return html_entity_decode($value, ENT_QUOTES, $this->encoding);
    }

    public function sanitize(string $value): string
    {
        $safeValue = filter_var($value, FILTER_SANITIZE_STRING);
        if(!$safeValue) {
            return '';
        }

        return $safeValue;
    }

    public function sanitizeAll(array $values): array
    {
        $safeValues = filter_var_array($values, FILTER_SANITIZE_STRING);
        if(!$safeValues) {
            return [];
        }

        return $safeValues;
    }

    public function sanitizeURL(string $url): string
    {
        $safeURL = filter_var($url, FILTER_SANITIZE_URL);
        if(!$safeURL) {
            return '';
        }

        return $safeURL;
    }
    
    public function sanitizeURLs(array $urls): array
    {
        $safeURls = filter_var_array($urls, FILTER_SANITIZE_URL);
        if(!$safeURls) {
            return [];
        }

        return $safeURls;
    }

    public function sanitizeHTML(string $value): string
    {
        if (!$this->validate($value)) {
            return '';
        }

        $this->escapeURLs($value);
        $this->removeSpacing($value);
        $this->removeNullCharacter($value);
        $this->removeNetscapeJSEntities($value);
      
        $doc = new DOMDocument("1.0", $this->encoding);
        libxml_use_internal_errors(false);

        $html = mb_convert_encoding("<html>${value}</html>", "HTML-ENTITIES", $this->encoding);

        if ($doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS)) {
            foreach ($doc->getElementsByTagName('*') as $tag) {
                if (in_array(strtolower($tag->tagName), $this->notAllowedTags) || $tag->nodeType === XML_CDATA_SECTION_NODE || $tag->nodeType === XML_COMMENT_NODE) {
                    $tag->parentNode->removeChild($tag);

                    continue;
                }

                foreach ($tag->attributes as $attr) {
                    if (in_array(strtolower($attr->nodeName), $this->notAllowedAttrs) || $attr->nodeType === XML_ATTRIBUTE_CDATA) {
                        $tag->removeAttribute($attr->nodeName);

                        continue;
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

    public function loadBlackList(): void
    {
        try {
            $blackListContent = $this->getDefaultBlackList();

            $blackListFileContent = @file_get_contents($this->blackListPath);
            if ($blackListFileContent !== false) {
                $blackListContent = json_decode($blackListFileContent, true);
            }

            if (is_array($blackListContent) && sizeof($blackListContent) > 0) {
                if (isset($blackListContent["tags"], $blackListContent["tags"]["not-allowed"]) && sizeof($blackListContent["tags"]["not-allowed"]) > 0) {
                    $this->notAllowedTags = $blackListContent["tags"]["not-allowed"];
                } else {
                    $this->notAllowedTags = self::NOT_ALLOWED_TAGS;
                }
                
                if (isset($blackListContent["tags"], $blackListContent["tags"]["not-allowed-empty"]) && sizeof($blackListContent["tags"]["not-allowed-empty"]) > 0) {
                    $this->notAllowedEmptyTags = $blackListContent["tags"]["not-allowed-empty"];
                } else {
                    $this->notAllowedEmptyTags = self::NOT_ALLOWED_EMPTY_TAGS;
                }
                
                if (isset($blackListContent["attributes"], $blackListContent["attributes"]["not-allowed"]) && sizeof($blackListContent["attributes"]["not-allowed"]) > 0) {
                    $this->notAllowedAttrs = $blackListContent["attributes"]["not-allowed"];
                } else {
                    $this->notAllowedAttrs = self::NOT_ALLOWED_ATTRS;
                }
            }
        } catch(Throwable $exception) {
            throw new BlackListNotLoadedException($exception);
        }
    }

    public function getDefaultBlackList(): array
    {
        return [
            'tags'                  => [
                'not-allowed'       => [],
                'not-allowed-empty' => []
            ],
            'attributes'            => [
                'not-allowed'       => []
            ]
        ];
    }

    private function getURLRegex(): string
    {
        return "((https?|http)://)?(www\.)?" // SCHEME
                . '([a-z0-9+!*(),;?&=$_.-]+(:[a-z0-9+!*(),;?&=$_.-]+)?@)?' // User and Pass
                . "([a-z0-9\-\.]*)\.(([a-z]{2,4})|([0-9]{1,3}\.([0-9]{1,3})\.([0-9]{1,3})))" // Host or IP
                . "(:[0-9]{2,5})?" // Port
                . '(/([a-z0-9+$_%-]\.?)+)*/?' // Path
                . '(\?[a-z+&\$_.-][a-z0-9;:@&%=+/$_.-]*)?' // GET Query
                . "(#[a-z_.-][a-z0-9+$%_.-]*)?"; // Anchor
    }

    private function escapeURLs(string &$value): string
    {
        preg_match_all($this->getURLRegex(), $value, $matches);

        $safeURLs = $this->sanitizeURLs($matches);
        foreach($matches as $key => $match) {
            if (!filter_var($safeURLs[$key], FILTER_VALIDATE_URL) || !isset($safeURLs[$key])) {
                $replaceWith = '#';
            }

            $value = str_replace($match, $replaceWith ?? $safeURLs[$key], $value);
        }

        return $value;
    }
    
    private function removeSpacing(string &$value): string
    {
        $value = preg_replace("(?:\s|\"|'|\+|&#x0[9A-F];|%0[9a-f])*?", '', $value);
        if (is_null($value) || is_array($value)) {
            return '';
        }

        return $value;
    }
    
    private function removeNullCharacter(string &$value): string
    {
        $value = preg_replace(chr(0), '', $value);
        if (is_null($value) || is_array($value)) {
            return '';
        }

        return $value;
    }
    
    private function removeNetscapeJSEntities(string &$value): string
    {
        $value = preg_replace("%&\\s*\\{[^}]*(\\}\\s*;?|$)%", '', $value);
        if (is_null($value) || is_array($value)) {
            return '';
        }

        return $value;
    }
}