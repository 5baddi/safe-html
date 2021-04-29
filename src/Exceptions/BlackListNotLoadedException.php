<?php

/**
 * Safe HTML
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */

namespace BADDIServices\SafeHTML\Exceptions;

use Exception;
use Throwable;

class BlackListNotLoadedException extends Exception
{
    public function __construct(Throwable $previous = null, string $message = "Failed to load blacklist file", int $code = 11) 
    {
        parent::__construct($message, $code, $previous);
    }
}