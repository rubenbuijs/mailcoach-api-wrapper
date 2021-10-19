<?php

namespace RubenBuijs\MailcoachApiWrapper\Exceptions;

use Exception;

class InvalidSubscriber extends Exception
{
    /**
     * @param $email
     * @return static
     */
    public static function noSubscriberFound($email)
    {
        return new static("There is no subscriber with email `{$email}`.");
    }

    /**
     * @param $email
     * @return static
     */
    public static function multipleSubscribersFound($email)
    {
        return new static("Retrieved more than one subscriber for `{$email}`.");
    }
}
