Mustache for XP Framework
=========================
The [mustache template language](http://mustache.github.io/) implemented for the XP Framework.

```php
$engine= new \com\github\mustache\MustacheEngine();
$transformed= $engine->render(
  'Hello {{name}}',
  array('name' => 'World')
);
```

Introduction
------------
Read the excellent [mustache man-page](http://mustache.github.io/mustache.5.html) for a start.

Features supported
------------------
This implementation supports all standard features of the [current specification](https://github.com/mustache/spec):

* Interpolation: `{{var}}` (`{{&var}}` and triple mustaches for unescaped) and the dot-notation. By default misses will be replaced by empty strongs.
* Comments: `{{! comment }}`. Comments will appear in parse tree but of course be excluded when rendering.
* Delimiters: `{{=@ @=}}` will change the starting and ending delimiter to the `@` sign. Arbitrary length delimiters are supported.
* Sections: `{{#section}}` as well as inverted sections: `{{^section}}` are supported.
* Partials: `{{> partial}}` will load a template called `partial.mustache` from the template loader.
* Implicit iterators: Written as `{{.}}`, the current loop value will be selected.

The optional *lambdas* module is also supported. 

Lambdas
-------
If the value is a closure, it will be invoked and the raw text will be passed to it:

### Template
```mustache
{{# wrapped }}
  {{ name }} is awesome.
{{/ wrapped }}
```

### Data
```php
array(
  'name'    => 'Willy',
  'wrapped' => function($text) {
    return '<b>'.$text.'</b>';
  }
);
```

### Output
```html
<b>Willy is awesome.</b>
```

The `$text` parameter passed is actually a `\com\github\mustache\Node` instance but may be treated as text as it overloads the string cast. In order to work with it, the node's `evaluate()` method can be called with the `\com\github\mustache\Context` instance given as the second argument:

```php
array(
  'name'    => 'Willy',
  'wrapped' => function($node, $context) {
    return '<b>'.strtoupper($node->evaluate($context)).'</b>';
  }
)
```