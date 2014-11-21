<?php

namespace mrjking\CSSTools\model;


class Style
{
    public $score;
    public $rules = [];
    public $md5;

    function __construct($rules, $score = 0)
    {
        foreach ($rules as $property => $value) {
            $this->rules[$property]= new Rule($property, $value);
        }
        ksort($this->rules);
        $this->md5 = md5($this->getString());
        $this->score = $score;
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

    public function getString()
    {
        $rules = [];
        foreach ($this->rules as $rule) {
            $rules []= $rule->getString();
        }
        return join(';', $rules);
    }

    public function getRulesArray()
    {
        $rules = [];
        foreach ($this->rules as $property => $rule) {
            $rules[$property] = $rule->value;
        }
        return $rules;
    }
}
