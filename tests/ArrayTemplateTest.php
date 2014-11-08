<?php

namespace Superruzafa\Template;

class ArrayTemplateTest extends TemplateTestAbstract
{
    protected function setUp()
    {
        $this->activateErrorHandler();
    }

    protected function tearDown()
    {
        $this->deactivateErrorHandler();
    }

    /** @test */
    public function renderEmptyArray()
    {
        $array = new ArrayTemplate();
        $this->assertEquals(array(), $array->render());
    }

    /** @test */
    public function renderNonEmptyArray()
    {
        $array = array('foo' => 'bar');
        $template = new ArrayTemplate($array);
        $this->assertEquals($array, $template->render());
    }

    /** @test */
    public function replacementWhenThereIsNoVariable()
    {
        $array = array('foo' => '{{ bar }}');
        $expected = array('foo' => '');
        $template = new ArrayTemplate($array);
        $this->assertEquals($expected, $template->render());
    }

    /** @test */
    public function singleReplacement()
    {
        $variables = array('bar' => 'xxx');
        $array = array('foo' => '{{ bar }}');
        $expected = array('foo' => 'xxx');
        $template = new ArrayTemplate($array);
        $this->assertEquals($expected, $template->render($variables));
    }

    /** @test */
    public function arrayReplacement()
    {
        $variables = array('bar' => array());
        $array = array('foo' => '{{ bar }}');
        $expected = array('foo' => 'Array');
        $template = new ArrayTemplate($array);
        $this->assertEquals($expected, $template->render($variables));
    }

    /** @test */
    public function recursiveReplacement()
    {
        $variables = array(
            'bar' => '{{ baz }}',
            'baz' => '{{ qux }}',
            'qux' => 'yyy'
        );
        $array = array('foo' => '{{ bar }}');
        $expected = array('foo' => 'yyy');
        $template = new ArrayTemplate($array);
        $this->assertEquals($expected, $template->render($variables));
    }

    /** @test */
    public function recursiveReplacementMissingVariable()
    {
        $variables = array(
            'bar' => '{{ baz }}',
            'baz' => '{{ qux }}',
        );
        $array = array('foo' => '{{ bar }}');
        $expected = array('foo' => '');
        $template = new ArrayTemplate($array);
        $this->assertEquals($expected, $template->render($variables));
        $this->assertError(E_USER_WARNING, 'Undefined key: "qux"');
    }

    /** @test */
    public function recursiveReplacementCyclingRecursion()
    {
        $variables = array(
            'bar' => '{{ baz }}',
            'baz' => '{{ qux }}',
            'qux' => '{{ bar }}',
        );
        $array = array('foo' => '{{ bar }}');
        $expected = array('foo' => '');
        $template = new ArrayTemplate($array);
        $this->assertEquals($expected, $template->render($variables));
        $this->assertError(E_USER_WARNING, 'Cyclic recursion: bar -> baz -> qux -> bar');
    }
}
