<?php

namespace mrjking\CSSTools;
use Mockery as m;

class NormalizerTest extends \PHPUnit_Framework_TestCase
{
    protected $normalizer;

    public function testNormalizeRemovesExtraWhiteSpace()
    {
        $style = ' margin-left:  10px;  top:  5px;  ';
        $expected = [
            'margin-left' => '10px',
            'top' => '5px'
        ];
        $this->assertSame($expected, $this->normalizer->normalize($style));
    }

    public function testNormalizeChangesZeroUnitsToJustZero()
    {
        $style = 'margin-left:0px;top:0em; width: 0%;';
        $expected = [
            'margin-left' => '0',
            'top' => '0',
            'width' => '0',
        ];
        $this->assertSame($expected, $this->normalizer->normalize($style));
    }

    public function testNormalizeRemovesEmptyRules()
    {
        $style = 'margin-left:10em; ; width: 25%;   ;';
        $expected = [
            'margin-left' => '10em',
            'width' => '25%',
        ];
        $this->assertSame($expected, $this->normalizer->normalize($style));
    }

    public function testNormalizeIgnoresBadRuleAndReturnsEmptyArray()
    {
        $style = 'margin : : -5px;';
        $this->assertSame([], $this->normalizer->normalize($style));
    }

    public function testNormalizeIgnoresBadRuleWithValidRules()
    {
        $style = 'left: 3px; margin : : -5px; top: 5px;';
        $expected = [
            'left' => '3px',
            'top' => '5px',
        ];
        $this->assertSame($expected, $this->normalizer->normalize($style));
    }

    public function setup()
    {
        $this->normalizer = new Normalizer();
    }
}
