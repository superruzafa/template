<?php

namespace Superruzafa\Template;

class ArrayTemplate implements Renderizable
{
    /** @var array */
    private $array;

    public function __construct(array $array = array())
    {
        $this->array = $array;
    }

    public function render(array $variables = array())
    {
        $variables = $variables ?: $this->array;

        $callback = function ($item) use ($variables) {
            if ($item instanceof Renderizable) {
                return $item->render($variables);
            } elseif (is_string($item)) {
                $string = new StringTemplate($item);
                return $string->render($variables);
            }
            return $item;
        };
        return array_map($callback, $this->array);
    }
}
