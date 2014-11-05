<?php

namespace mrjking\CSSTools;

use mrjking\CSSTools\Model\Style;

class Normalizer
{
    protected static $zeroUnits = [
        '0em',
        '0ex',
        '0ch',
        '0rem',
        '0vh',
        '0vw',
        '0vmin',
        '0vmax',
        '0px',
        '0%',
        '0mm',
        '0cm',
        '0in',
        '0pt',
        '0pc',
        '0mozmm'
    ];

    protected $zeroUnitRegexes;

    function __construct()
    {
        $this->zeroUnitRegexes = array_map(function($unit){return '/(\s|:)(' . $unit . ')/i';}, self::$zeroUnits);
    }

    public function normalize($style)
    {
        $style = $this->normalizeWhiteSpace($style);
        $style = $this->fixZeroUnits($style);

        //Break apart rules and discard any empties
        $rules = explode(';', $style);
        $formattedRules = [];
        foreach ($rules as $rule) {
            $rule = $this->cleanRule($rule);
            if (!empty($rule)) {
                $formattedRules[$rule['property']]= $rule['value'];
            }
        }

        return new Style($formattedRules);
    }

    protected function normalizeWhiteSpace($style)
    {
        return preg_replace('/\s+/', ' ',  $style); //replaces 1 or more spaces with just one
    }

    protected function fixZeroUnits($style)
    {
        return preg_replace($this->zeroUnitRegexes, '${1}0', $style); //Replace 0px with 0 (same thing, faster)
    }

    protected function cleanRule($rule)
    {
        $rule = trim($rule);
        $parts = explode(':', $rule);
        if (count($parts) !== 2) {
            //Bad rule
            return [];
        }
        $property = trim($parts[0]);
        $value = trim($parts[1]);
        return ['property' => $property, 'value' => $value];
    }
}
