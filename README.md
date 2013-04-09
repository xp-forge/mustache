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
* Comments: `{{! comment }}`. Comments will appear in the parse tree but of course will be excluded when rendering.
* Delimiters: `{{=@ @=}}` will change the starting and ending delimiter to the `@` sign. Arbitrary length delimiters are supported.
* Sections: `{{#section}}` as well as inverted sections: `{{^section}}` are supported.
* Partials: `{{> partial}}` will load a template called `partial.mustache` from the template loader.
* Implicit iterators: Written as `{{.}}`, the current loop value will be selected.

The optional *lambdas* module is also supported. 

Lambdas
-------
If the value is a closure, it will be invoked and the raw text (no interpolations will have been performed!) will be passed to it:

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

### Extended usage
The `$text` parameter passed is actually a `\com\github\mustache\Node` instance but may be treated as text as it overloads the string cast. In order to work with it, the node's `evaluate()` method can be called with the `\com\github\mustache\Context` instance given as the second argument:

```php
array(
  'name'    => 'Willy',
  'wrapped' => function($node, $context) {
    return '<b>'.strtoupper($node->evaluate($context)).'</b>';
  }
)
```

Template loading
----------------
Per default, templates are loaded from the current working directory. This can be changed by passing a template loader instance to the engine:

```php
$engine= new \com\github\mustache\MustacheEngine();
$engine->withTemplates(new InFiles(new Folder('templates')));
$transformed= $engine->transform('hello', array('name' => 'World'));
```

This will load the template stored in the file `templates/hello.mustache`. This template loader will also be used for partials.

Helpers
-------
Think of helpers as "omnipresent" context. They are added to the engine instance via `withHelper()` and will be available in any rendering context invoked on that instance.

### Template
```mustache
{{# bold }}
  This is {{ location }}!
{{/ bold }}
```

### Call
```php
$engine= new \com\github\mustache\MustacheEngine();
$engine->withHelper('bold', function($text) {
  return '<b>'.$text.'</b>';
});
$transformed= $engine->render($template, array('location' => 'Spartaaaaa'));
```

### Output
```html
<b>This is Spartaaaaa!</b>
```

Releases
--------
The current release is version 1.0.0, available via http://builds.planet-xp.net/xp-forge/mustache/1.0.0/xp-mustache-1.0.0.xar.

Spec compliance
---------------
Whether this implementation is compliant with the official spec can be tested as follows:

```sh
$ wget 'https://github.com/mustache/spec/archive/master.zip' -O master.zip
$ unzip master.zip && rm master.zip
$ unittest com.github.mustache.** -a spec-master/specs/ spec-master/specs
```