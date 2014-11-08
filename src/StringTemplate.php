<?php

namespace Superruzafa\Template;

class StringTemplate implements Renderizable
{
    const PATTERN = '/\{\{\s*((?:(?!}})\S)+)\s*}}/';

    /** @var string */
    private $string;

    /**
     * Creates a new String
     *
     * @param string $string
     */
    public function __construct($string = '')
    {
        $this->string = $string;
    }

    /**
     * Gets the string
     *
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     * Sets the string
     *
     * @param string $string
     * @return String
     */
    public function setString($string)
    {
        $this->string = $string;
        return $this;
    }

    /** {@inheritdoc} */
    public function render(array $variables = array())
    {
        $pregCallback = function ($match) use (&$data) {
            list(, $data['key']) = $match;
            return call_user_func_array($data['resolverCallback'], array(&$data));
        };

        $resolverCallback = function (&$data) {
            $key            = $data['key'];
            $pregCallback   = $data['pregCallback'];
            $variables      = $data['variables'];
            $stack          = &$data['stack'];
            $solvedKeys     = &$data['solvedKeys'];

            if (isset($solvedKeys[$key])) {
                return $solvedKeys[$key];
            }

            if (in_array($key, $stack)) {
                trigger_error(sprintf('Cyclic recursion: %s -> %s', implode(' -> ', $stack), $key), E_USER_WARNING);
                return $solvedKeys[$key] = '';
            }

            if (!isset($variables[$key])) {
                trigger_error(sprintf('Undefined key: "%s"', $key), E_USER_WARNING);
                return $solvedKeys[$key] = '';
            }

            if (!is_string($variables[$key])) {
                return $solvedContext[$key] = strval($variables[$key]);
            }

            array_push($stack, $key);
            $solvedKeys[$key] = preg_replace_callback(StringTemplate::PATTERN, $pregCallback, $variables[$key]);
            array_pop($stack);
            return $solvedKeys[$key];
        };

        $data = array(
            'key'               => '',
            'variables'         => $variables,
            'resolverCallback'  => $resolverCallback,
            'pregCallback'      => $pregCallback,
            'stack'             => array(),
            'solvedKeys'        => array(),
        );

        return preg_replace_callback(self::PATTERN, $pregCallback, $this->string);
    }
}
