<?php

namespace Superruzafa\Template;

class TemplateTestAbstract extends \PHPUnit_Framework_TestCase
{
    private static $errors = array();

    protected function activateErrorHandler()
    {
        set_error_handler('Superruzafa\\Template\\TemplateTestAbstract::errorHandler');
        self::$errors = array();
    }

    protected function deactivateErrorHandler()
    {
        restore_error_handler();
    }

    protected function assertError($code, $message)
    {
        $this->assertContains($code, self::$errors['codes']);
        $this->assertContains($message, self::$errors['messages']);
    }

    public static function errorHandler($code, $message)
    {
        self::$errors['codes'][] = $code;
        self::$errors['messages'][] = $message;
    }
}
