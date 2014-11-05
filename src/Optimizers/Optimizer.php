<?php
/**
 * Created by PhpStorm.
 * User: Jerod
 * Date: 11/1/2014
 * Time: 11:28 PM
 */

namespace mrjking\CSSTools\Optimizers;

class Optimizer
{
    protected $ruleUsage = [];

    protected $sharedFactor = [];
    protected $sharedBreakdown = [];

    protected $finalStyles = [];

    protected $minRules;
    protected $minFactor;
    protected $styles;



    function __construct($styles, $minRules = 2, $minFactor = 15)
    {
        if ($minRules < 1) {
            $minRules = 1;
        }

        $this->minRules = $minRules;
        $this->minFactor = $minFactor;
        $this->styles = [];

        foreach ($styles as $style) {
            if ($style->ruleCount() >= $this->minRules) {
                $this->styles []= $style;
            }
        }
    }

    public function calculateRuleUsage()
    {
        foreach ($this->styles as $style) {
            foreach ($style->rules as $ruleObj) {
                $key = $ruleObj->getString();
                if (!isset($this->ruleUsage[$key])) {
                    $this->ruleUsage[$key] = 0;
                }
                $this->ruleUsage[$key]++;

                if ($this->ruleUsage[$key] > $this->maxRuleUsage) {
                    $this->maxRuleUsage = $this->ruleUsage[$key];
                }
            }
        }
    }

    public function calculateStyleSharedFactor()
    {
        foreach ($this->styles as $style) {
            $sharedFactor = 0;

            foreach ($style->rules as $ruleObj) {
                $key = $ruleObj->getString();
                $factor = $this->ruleUsage[$key] - 1;
                $sharedFactor += $factor;
            }

            $style->factor = round($sharedFactor / $style->ruleCount());
        }

        //Sort from highest to lowest (higher is better)
        uasort($this->styles, function($a, $b) {
            return $b->factor > $a->factor;
        });
    }

    public function calculateStyleMostNormalizedStyles()
    {
        foreach ($this->styles as $style) {
            //First calculate mean for all rules in this style
            $total = 0;
            foreach ($style->rules as $ruleObj) {
                $key = $ruleObj->getString();
                $total += $this->ruleUsage[$key];
            }
            $mean = round($total / $style->ruleCount());

            //Now calculate average deviation
            $total = 0;
            foreach ($style->rules as $ruleObj) {
                $key = $ruleObj->getString();
                $deviation = abs($this->ruleUsage[$key] - $mean);
                $total += $deviation;
            }
            $meanDeviation = $total / $style->ruleCount();
            $style->factor = $meanDeviation;
        }

        //Sort from lowest to highest (lower is better)
        uasort($this->styles, function($a, $b) {
            return $b->factor < $a->factor;
        });
    }

    public function calculateFinalStyles()
    {
        $styleStart = count($this->styles);

        while (!empty($this->styles)) {
            $currentStyle = array_shift($this->styles);
            if (!$currentStyle->isEmpty()) {
                $currentStyle = $currentStyle->getRulesArray();
                $this->finalStyles [] = $currentStyle;

                foreach ($this->styles as $style) {
                    //Determine if this style has all of the rules of the finalStyle
                    if ($style->containAllRules($currentStyle)) {
                        $style->removeRulesWithProperties(array_keys($currentStyle));
                    }
                }
            }
        }

        echo 'Final styles: ' . PHP_EOL . json_encode($this->finalStyles, JSON_PRETTY_PRINT) . PHP_EOL;
        echo 'Final style count: ' . count($this->finalStyles) . PHP_EOL;
        echo 'Original style count: ' . $styleStart . PHP_EOL;
    }


    public function echoStyles()
    {
        foreach($this->ruleUsage as $key => $value) {
            echo str_pad($key, 38) . " =>\t" . json_encode($value) . PHP_EOL;
        }
    }

    public function echoSharedPercentages()
    {
        foreach($this->sharedFactor as $key => $value) {
            echo str_pad($key, 3) . " =>\t" . $value . PHP_EOL;
        }
    }

    public function echoSharedPercentagesBreakdown()
    {
        foreach($this->styles as $key => $style) {
            echo 'Style #' . str_pad($key, 3) . '(Factor ' . round($style->factor, 3) . ") =>\t" . json_encode($style->getRulesArray()) . PHP_EOL;
        }
    }

    public function getStyles()
    {
        uasort($this->ruleUsage, function($a, $b){
            return count($b) > count($a);
        });
        return $this->ruleUsage;
    }
 }
