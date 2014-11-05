<?php

namespace mrjking\CSSTools;

/**
 * Finds the inline CSS style rules, replaces their style code with unique class code, saves style in array
 * Example: style="top: 40px;" changes to class="style1", $styleTags['style1'] = 'top:40px'
 *
 * @package mrjking\CSSCleaner
 */
class Finder
{
    protected $styleTags = [];
    protected $prefix;

    /**
     * Attach a prefix to the class names that are auto generated
     * @param string $prefix
     */
    function __construct($prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * Finds all style="" tags in the HTML and replaces them with class="style1", where the 1 increase
     * for each style found. The original style tag is saved in the styleTags array.
     *
     * @param $html HTML to be parsed
     * @return mixed HTML that has been altered to remove style tags
     */
    public function parse($html)
    {
        $html = preg_replace_callback('/style\s*=\s*\"{1}([^\"]+)\"{1}/i', function($matches){
            $index = $this->saveStyle($matches[1]);
            return 'class="' . $index . '"';
        }, $html);

        return $html;
    }

    /**
     * Returns the style array that was created from a call to parse
     *
     * @return array
     */
    public function getStyles()
    {
        return $this->styleTags;
    }

    private function saveStyle($style)
    {
        $count = count($this->styleTags) + 1;
        $index = $this->prefix . 'style' . $count;
        $this->styleTags[$index] = $style;
        return $index;
    }
}
