<?php

namespace mrjking\CSSTools\Optimizers;

class Optimizer implements OptimizerInterface
{
    protected $ruleUsage = [];
    protected $minRules;
    protected $minScore;

    function __construct($minRules = 2, $minScore = 2)
    {
        if ($minRules < 1) {
            $minRules = 1;
        }

        $this->minRules = $minRules;
        $this->minScore = $minScore;
    }

    protected function calculateRuleUsage($styles)
    {
        foreach ($styles as $style) {
            foreach ($style->rules as $ruleObj) {
                $key = $ruleObj->getString();
                if (!isset($this->ruleUsage[$key])) {
                    $this->ruleUsage[$key] = 0;
                }
                $this->ruleUsage[$key]++;
            }
        }
    }

    /**
     *
     */
    public function process()
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

    public function optimize(Array $rawStyles)
    {
        $styles = [];
        foreach ($rawStyles as $style) {
            if ($style->ruleCount() >= $this->minRules) {
                $styles []= $style;
            }
        }

        $this->calculateRuleUsage($styles);
        $styleStart = count($this->styles);

        $finalStyles = [];
        while (!empty($styles)) {
            $currentStyle = array_shift($styles);
            if (!$currentStyle->isEmpty()) {
                $currentStyle = $currentStyle->getRulesArray();
                $finalStyles [] = $currentStyle;

                foreach ($styles as $style) {
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
 }
