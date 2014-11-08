<?php

namespace Superruzafa\Template;

class StringTemplateTest extends TemplateTestAbstract
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
    public function renderEmptyString()
    {
        $string = new StringTemplate();
        $this->assertEquals('', $string->render());
    }

    /** @test */
    public function renderStringWithoutReplacements()
    {
        $string = new StringTemplate('the same string');
        $this->assertEquals('the same string', $string->render());
    }

    /** @test */
    public function renderStringWithReplacementsButWithoutVariables()
    {
        $string = new StringTemplate('the >{{ variable }}< string');
        $this->assertEquals('the >< string', $string->render());
    }

    /** @test */
    public function renderStringWithReplacementsAndVariables()
    {
        $string = new StringTemplate('the >{{ variable }}< string');
        $variables = array('variable' => 'xxx');
        $this->assertEquals('the >xxx< string', $string->render($variables));
    }

    /** @test */
    public function recursiveReplacement()
    {
        $string = new StringTemplate('the >{{ foo }}< string');
        $variables = array(
            'foo' => '{{ bar }}',
            'bar' => 'xxx',
        );
        $this->assertEquals('the >xxx< string', $string->render($variables));
    }

    /** @test */
    public function recursiveReplacementCyclingRecursion()
    {
        $string = new StringTemplate('the >{{ foo }}< string');
        $variables = array(
            'foo' => '{{ bar }}',
            'bar' => '{{ baz }}',
            'baz' => '{{ foo }}',
        );
        $this->assertEquals('the >< string', $string->render($variables));
        $this->assertError(E_USER_WARNING, 'Cyclic recursion: foo -> bar -> baz -> foo');
    }
}
