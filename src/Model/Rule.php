<?php
/**
 * Created by PhpStorm.
 * User: Jerod
 * Date: 11/3/2014
 * Time: 11:07 PM
 */

namespace mrjking\CSSTools\model;


class Rule
{
    public $property;
    public $value;

    function __construct($property, $value)
    {
        $this->property = $property;
        $this->value = $value;
    }

    public function getString()
    {
        return $this->property . ':' . $this->value;
    }
}
