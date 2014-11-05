<?php

namespace mrjking\CSSTools\Optimizers;


class AverageDeviationOptimizer extends Optimizer
{


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
}
