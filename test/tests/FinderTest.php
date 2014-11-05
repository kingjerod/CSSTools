<?php

namespace mrjking\CSSTools;
use Mockery as m;



class FinderTest extends \PHPUnit_Framework_TestCase
{
    private $finder;

    public function testParseSingle()
    {
        $html = '<div><span style="left: 10px;"></span></div>';
        $result = $this->finder->parse($html);
        $expected = '<div><span class="ABCstyle1"></span></div>';
        $this->assertSame($expected, $result);

        $styles = $this->finder->getStyles();
        $this->assertCount(1, $styles);
        $this->assertEquals(['ABCstyle1' => 'left: 10px;'], $styles);
    }

    public function testParseMultiple()
    {
        $html = '<div><span style="left: 10px;"></span> <a style="top: 25px; color: #FFF;">Test</a></div>';
        $result = $this->finder->parse($html);
        $expected = '<div><span class="ABCstyle1"></span> <a class="ABCstyle2">Test</a></div>';
        $this->assertSame($expected, $result);

        $styles = $this->finder->getStyles();
        $this->assertCount(2, $styles);
        $this->assertEquals(['ABCstyle1' => 'left: 10px;', 'ABCstyle2' => 'top: 25px; color: #FFF;'], $styles);
    }

    public function setup()
    {
        $this->finder = new Finder('ABC');
    }
}
