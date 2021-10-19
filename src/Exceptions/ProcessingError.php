<?php

namespace RubenBuijs\MailcoachApiWrapper\Exceptions;

use Exception;

class ProcessingError extends Exception
{
    /**
     * @return static
     */
    public static function invalidCredentials()
    {
        return new static("Please set configuration for MAILCOACH_API_BASE_URL, MAILCOACH_API_TOKEN, and MAILCOACH_LIST_ID.");
    }
}
