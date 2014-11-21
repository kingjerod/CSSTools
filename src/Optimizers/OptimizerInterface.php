<?php

namespace mrjking\CSSTools\Optimizers;

interface OptimizerInterface
{
    /**
     * Processes the styles and determines which ones are the best for optimizing. This is done by calculating the
     * score for each style. Once the scores are determined the styles can be sorted from best -> worst.
     *
     * @param array $styles
     * @return mixed
     */
    function process(Array $styles);

    /**
     * Public function that takes an array of styles and figures out how to optimize them. First it sorts out
     * styles that don't meet the requirements of the optimizer (min # rules) and then it calls process() on them.
     * Once the styles are processed, the final style rules are calculated that have the min score.
     *
     * @param array $rawStyles
     * @return mixed
     */
    function optimize(Array $rawStyles);
}