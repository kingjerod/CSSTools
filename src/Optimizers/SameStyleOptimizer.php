<?php

namespace mrjking\CSSTools\Optimizers;

class SameStyleOptimizer extends Optimizer
{
    public function calculate()
    {
        $styleCounts = [];

        //Calculate counts for each style
        foreach ($this->styles as $style) {
            if(!isset($styleCounts[$style->md5])) {
                $styleCounts[$style->md5] = 0;
            }
            $styleCounts[$style->md5]++;
        }


        //Put the counts into score for each style (higher = better)
        $uniqueStyles = [];
        foreach ($this->styles as $style) {
            $style->score = $styleCounts[$style->md5];
            $uniqueStyles[$style->md5] = $style;
        }

        $this->styles = $uniqueStyles; //only need unique styles now

        //Sort from highest to lowest (higher is better)
        uasort($this->styles, function($a, $b) {
            return $b->score > $a->score;
        });
    }

    public function calculateFinalStyles()
    {
        $styleStart = count($this->styles);

        while (!empty($this->styles)) {

            $currentStyle = array_shift($this->styles);
            if (!$currentStyle->isEmpty() && $currentStyle->score > $this->minScore) {
                $currentStyle = $currentStyle->getRulesArray();
                $this->finalStyles [] = $currentStyle;
            }
        }

        echo 'Final styles: ' . PHP_EOL . json_encode($this->finalStyles, JSON_PRETTY_PRINT) . PHP_EOL;
        echo 'Final style count: ' . count($this->finalStyles) . PHP_EOL;
        echo 'Original style count: ' . $styleStart . PHP_EOL;
    }
}