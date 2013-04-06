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