Template
========

A recursive template engine.

StringTemplate
--------------

The most basic template is the string template. It replaces strings containing ```{{ variables }}``` by the values passed as replacements:

``` php
$replacements = array(
    'variable' => 'replacement'
);
$string = 'this string contains a {{ variable }}';
$template = new StringTemplate($string);
echo $template->render($replacements);
// 'this string contains a replacement'
```

ArrayTemplate
-------------

The array template renders its values as ```StringTemplate``` does.

``` php
$replacements = array(
    'vehicle' => 'car',
    'color'   => 'dark gray'
);
$array = array(
    'My {{ vehicle }} color is {{ color }}',
    'Because {{ color }} is my favourite color',
    'I also likes the {{ alternative-color }} color'
);
$template = new ArrayTemplate($array);
var_dump($template->render($replacements));
// array(
//    'My car color is dark gray',
//    'Because dark gray is my favourite color',
//    'I also likes the  color'
// );
```

Arrays self-rendering
---------------------

Pretty straightforward, isn't it?
An array template can be self-rendered by using itself as provider for replacements!

``` php
$array = array(
    'vehicle' => 'bicycle',
    'color'   => 'blue',
    'phrase'  => 'My {{ vehicle }} color is {{ color }}',
);
$template = new ArrayTemplate($array);
var_dump($template->render());
// array(
//    'vehicle' => 'bicycle',
//    'color'   => 'blue',
//    'phrase'  => 'My bicycle color is blue',
// )
```

Replacements may include other sub-replacements:

``` php
$array = array(
    'title'      => '{{ name }} {{ job }}',
    'name'       => '{{ firstName }} {{ surName }}',
    'firstName'  => 'Philip {{ middleName }}',
    'middleName' => 'J.',
    'surName'    => 'Fry',
    'job'        => 'Slacker delivery boy'
);
$template = new ArrayTemplate($array);
var_dump($template->render());
// array(
//     'title'      => 'Philip J. Fry, Slacker delivery boy',
//     'name'       => 'Philip J. Fry',
//     'firstName'  => 'Philip J.',
//     'middleName' => 'J.',
//     'surName'    => 'Fry'
// );
```

### Cyclic references

Cyclic references would be detected, replaced by an empty string and notified by a warning:

``` php
$love_triangle = array(
    'me'  => 'I like {{ you }}',
    'you' => 'You like {{ she }}',
    'she' => 'She likes {{ me }}'
);
$template = new ArrayTemplate($love_triangle);
var_dump($template->render());

// Warning: Cyclic recursion: you -> she -> me -> you in ...
// Warning: Cyclic recursion: she -> me -> you -> she in ...
// Warning: Cyclic recursion: me -> you -> she -> me in ...

// array(
//  'me'  => 'I like You like She likes I like ',
//  'you' => 'You like She likes I like You like ',
//  'she' => 'She likes I like You like She likes '
// )
```

Weird, isn't it?
The engine stores in a stack the variables that are beign replaced. When one of these variables whose value is being rendered is found then the cyclic recursion is detected.
This is the steps the engine follows to resolve for example the "me" entry:

```
1) me = 'I like {{ you }}'                   <- Replace {{ you }} by 'You like {{ she }}'
2) me = 'I like You like {{ she }}'          <- Replace {{ she }} by 'She likes {{ me }}'
3) me = 'I like You like She likes {{ me }}' <- Cyclic recursion detected, *me* is already being resolved
```
