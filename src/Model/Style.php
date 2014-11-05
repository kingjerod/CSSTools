<?php

namespace mrjking\CSSTools\model;


class Style
{
    public $factor;
    public $rules = [];

    function __construct($rules, $factor = 0)
    {
        foreach ($rules as $property => $value) {
            $this->rules[$property]= new Rule($property, $value);
        }
        $this->factor = $factor;
    }

    public function containAllRules($rules)
    {
        $selfRawRules = $this->getRulesArray();
        $intersection = array_intersect_assoc($selfRawRules, $rules);
        return count($intersection) == count($rules);
    }

    public function removeRulesWithProperties($properties)
    {
        foreach ($properties as $property) {
            unset($this->rules[$property]);
        }
    }

    public function isEmpty()
    {
        return empty($this->rules);
    }

    public function ruleCount()
    {
        return count($this->rules);
    }

    public function getRulesArray()
    {
        $rules = [];
        foreach ($this->rules as $property => $ruleObj) {
            $rules[$property] = $ruleObj->value;
        }
        return $rules;
    }
}
