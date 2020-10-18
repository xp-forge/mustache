Mustache
========

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-forge/mustache.svg)](http://travis-ci.org/xp-forge/mustache)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Requires PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.png)](http://php.net/)
[![Latest Stable Version](https://poser.pugx.org/xp-forge/mustache/version.png)](https://packagist.org/packages/xp-forge/mustache)

The [mustache template language](http://mustache.github.io/) implemented for the XP Framework.

```php
use com\github\mustache\MustacheEngine;

$transformed= (new MustacheEngine())->render(
  'Hello {{name}}',
  ['name' => 'World']
);
```

Introduction
------------
Read the excellent [mustache man-page](http://mustache.github.io/mustache.5.html) for a start.

Features supported
------------------
This implementation supports all standard features of the [current specification](https://github.com/mustache/spec):

* Interpolation: `{{var}}` (`{{&var}}` and triple mustaches for unescaped) and the dot-notation. By default misses will be replaced by empty strings.
* Comments: `{{! comment }}`. Comments will appear in the parse tree but of course will be excluded when rendering.
* Delimiters: `{{=@ @=}}` will change the starting and ending delimiter to the `@` sign. Arbitrary length delimiters are supported.
* Sections: `{{#section}}` as well as inverted sections: `{{^section}}` are supported.
* Partials: `{{> partial}}` will load a template called `partial.mustache` from the template loader.
* Implicit iterators: Written as `{{.}}`, the current loop value will be selected.

The optional *lambdas* module as well as the parent context extension (`../name`) are also supported.

Lambdas
-------
If the value is a closure, it will be invoked and the raw text (no interpolations will have been performed!) will be passed to it:

### Template
```handlebars
{{# wrapped }}
  {{ name }} is awesome.
{{/ wrapped }}
```

### Data
```php
[
  'name'    => 'Willy',
  'wrapped' => function($text) {
    return '<b>'.$text.'</b>';
  }
];
```

### Output
```html
<b>Willy is awesome.</b>
```

### Extended usage
The `$text` parameter passed is actually a `com.github.mustache.Node` instance but may be treated as text as it overloads the string cast. In order to work with it, the node's `evaluate()` method can be called with the `com.github.mustache.Context` instance given as the second argument:

```php
[
  'name'    => 'Willy',
  'wrapped' => function($node, $context) {
    return '<b>'.strtoupper($node->evaluate($context)).'</b>';
  }
]
```

Template loading
----------------
Per default, templates are loaded from the current working directory. This can be changed by passing a template loader instance to the engine:

```php
use com\github\mustache\{MustacheEngine, FilesIn};
use io\Folder;

$engine= new MustacheEngine();
$engine->withTemplates(new FilesIn(new Folder('templates')));
$transformed= $engine->transform('hello', ['name' => 'World']);
```

This will load the template stored in the file `templates/hello.mustache`. This template loader will also be used for partials.

Templates can also be loaded from the class loader, use the `com.github.mustache.ResourcesIn` and pass it a class loader instance (e.g. `ClassLoader::getDefault()` to search in all class paths) for this purpose.

Compiled templates
------------------
If you wish to apply variables to a template more than once, you can speed that process up by precompiling templates and using them later on:

```php
use com\github\mustache\MustacheEngine;

$engine= new MustacheEngine();
$template= $engine->compile($template);

// Later on:
$result1= $engine->evaluate($template, $variables1);
$result2= $engine->evaluate($template, $variables2);
```

Helpers
-------
Think of helpers as "omnipresent" context. They are added to the engine instance via `withHelper()` and will be available in any rendering context invoked on that instance.

### Template
```handlebars
{{# bold }}
  This is {{ location }}!
{{/ bold }}
```

### Call
```php
use com\github\mustache\MustacheEngine;

$engine= new MustacheEngine();
$engine->withHelper('bold', function($text) {
  return '<b>'.$text.'</b>';
});
$transformed= $engine->render($template, ['location' => 'Spartaaaaa']);
```

### Output
```html
<b>This is Spartaaaaa!</b>
```

### Using objects
You can also use instance methods as helpers, e.g.

```php
// Declaration
class LocalizationHelpers {
  public function date($list, $context) {
    return $context->lookup($list->nodeAt(0)->name())->toString('d.m.Y');
  }

  public function money($list, $context) {
    // ...
  }
}

// Usage with engine instance
$engine->withHelper('local', new LocalizationHelpers());
```

```handlebars
{{#local.date}}{{date}}{{/local.date}}
```

Spec compliance
---------------
Whether this implementation is compliant with the official spec can be tested as follows:

```sh
$ wget 'https://github.com/mustache/spec/archive/master.zip' -O master.zip
$ unzip master.zip && rm master.zip
$ xp test com.github.mustache.** -a spec-master/specs/
```
