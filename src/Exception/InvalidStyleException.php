<?php
/**
 * Created by PhpStorm.
 * User: jking
 * Date: 10/29/2014
 * Time: 9:51 PM
 */

namespace mrjking\CSSTools\Exception;


class InvalidStyleException extends \Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        if ($message == null) {
            $this->message = 'There was an error with one of the style tags, it appears invalid.';
        }
        parent::__construct($message, $code, $previous);
    }

}
