Mustache for XP Framework ChangeLog
===================================

## ?.?.? / ????-??-??

## 9.0.0 / 2025-05-04

* **Heads up:** Remove deprecated template loader infrastructure, see #8
  (@thekid)
* **Heads up:** Dropped support for PHP < 7.4, see xp-framework/rfc#343
  (@thekid)
* Added PHP 8.5 to test matrix - @thekid

## 8.2.0 / 2024-03-24

* Made compatible with XP 12 - @thekid

## 8.1.0 / 2023-11-04

* Merged PR #16: Try using `__get()` to retrieve pointer - @thekid
* Merged PR #15: Refactor code base to no longer use reflection - @thekid
* Added PHP 8.4 to the test matrix - @thekid

## 8.0.4 / 2023-10-28

* Fixed `com.github.mustache.Template` string representation to include
  root node's string representation
  (@thekid)

## 8.0.3 / 2023-03-18

* Fixed *Call to undefined method xp::stringOf()* - @thekid
* Merged PR #14: Migrate to new testing library - @thekid

## 8.0.2 / 2022-02-27

* Fixed member declaration in `com.github.mustache.templates.Compiled`
  (@thekid)

## 8.0.1 / 2022-01-30

* Fixed issue #13: Creation of dynamic property ... is deprecated, thereby
  adding compatibility with PHP 8.2
  (@thekid)

## 8.0.0 / 2021-10-21

* Made compatible with PHP 8.1 - add `ReturnTypeWillChange` attributes to
  iterator, see https://wiki.php.net/rfc/internal_method_return_types
* Implemented xp-framework/rfc#341, dropping compatibility with XP 9
  (@thekid)

## 7.0.0 / 2021-05-02

* Changed single quotes to be emitted as `&#039;` for all PHP versions.
  This breaks backwards compatiblity but ensure there are no security
  risks with expressions such as `<a href='{{url}}'>...</a>`.
  (@thekid)

## 6.1.2 / 2021-05-02

* Fixed single quotes being output as `&#039;` in PHP 8.1, which changed
  the flags' argument default, see php/php-src#6583
  (@thekid)

## 6.1.1 / 2021-02-15

* Fixed *Passing null to parameter ... of type string is deprecated* 
  warnings in PHP 8.1
  (@thekid)

## 6.1.0 / 2020-12-29

* Merged PR #11: Replace Tokens class by two separate & use-case optimized
  versions. Includes a 10x performance improvement for loading templates'
  source code
  (@thekid)

## 6.0.1 / 2020-12-28

* Fixed issue #10: Template code missing `1` - @thekid

## 6.0.0 / 2020-10-18

* Converted test suite to PHP 8 attributes and Assert DSL - @thekid
* Implemented xp-framework/rfc#334: Drop PHP 5.6:
  . **Heads up:** Minimum required PHP version now is PHP 7.0.0
  . Rewrote code base, grouping use statements
  . Converted `newinstance` to anonymous classes
  . Rewrote `isset(X) ? X : default` to `X ?? default`
  (@thekid)

## 5.3.3 / 2020-04-05

* Implemented RFC #335: Remove deprecated key/value pair annotation syntax
  (@thekid)

## 5.3.2 / 2019-12-01

* Made compatible with XP 10 - @thekid

## 5.3.1 / 2019-08-20

* Made compatible with PHP 7.4 - refrain using `{}` for string offsets
  (@thekid)

## 5.3.0 / 2018-03-14

* Added PHP 7.2 to test matrix - @thekid
* Merged PR #9: Add MustacheEngine::write(), which is like evaluate()
  but writes to a stream instead of returning the contents as a string
  (@thekid)

## 5.2.0 / 2017-09-18

* Made parsing target exchangeable by overriding `target()` method.
  (@thekid)

## 5.1.0 / 2017-09-17

* Merged pull request #8: Refactor template loading mechanism
  (@thekid)

## 5.0.1 / 2017-06-12

* Consistently pass node, context, options to all expressions
  (@thekid)

## 5.0.0 / 2017-07-03

* Added forward compatibility with XP 9.0.0 - @thekid

## 4.0.0 / 2016-08-28

* **Heads up: Dropped PHP 5.5 support!** - @thekid
* Added forward compatibility with XP 8.0.0 - @thekid

## 3.2.1 / 2016-05-17

* Fixed code to also pass options to instance method helpers.
  (@thekid)

## 3.2.0 / 2016-05-16

* Replaced checks for instances of lang.Generic with `is_object()`
  (@thekid)

## 3.1.0 / 2016-05-16

* Merged PR #6: Implement array-style functionality for NodeList
  (@thekid)
* Fixed handling of empty lines: The input `a\n\nb` used to produce
  two text nodes: `a\n` and `\nb` causing unexpected indenting. The
  same input now produces empty lines as separate nodes, and in this
  example these three nodes: `a\n`, `\n` and `b`.
  (@thekid)
* Merged PR #5: Add an operation to list templates in a loader
  (@thekid)

## 3.0.0 / 2016-02-21

* Added version compatibility with XP 7 - @thekid

## 2.0.2 / 2016-01-23

* Fix code to use `nameof()` instead of the deprecated `getClassName()`
  method from lang.Generic. See xp-framework/core#120
  (@thekid)

## 2.0.1 / 2015-12-20

* Declared dependency on xp-framework/tokenize and xp-framework/unittest,
  which have since been extracted from XP core.
  (@thekid)

## 2.0.0 / 2015-12-14

* **Heads up**: Changed minimum XP version to XP 6.5.0, and with it the
  minimum PHP version to PHP 5.5.
  (@thekid)

## 1.4.1 / 2015-11-23

* Fixed `com.github.mustache.unittest.SpecificationTest` raising
  exceptions with XP 6.4.0+
  (@thekid)
* Changed `xp-forge/json` dependency to require-dev only
  (@kiesel)

## 1.4.0 / 2015-07-12

* Added forward compatibility with XP 6.4.0:
  . Rewrote `raise()` call to throw - see xp-framework/core#89
  . Rewrote all occurrences using `create()` to use PHP 5.4 syntax
  . Used short array syntax in various places
  (@thekid)
* Added preliminary PHP 7 support (alpha2 and beta1)
  (@thekid)

## 1.3.4 / 2015-04-07

* Fixed implicit iterators, which should allow iterating over nested 
  arrays according to the spec.
  (@thekid)

## 1.3.3 / 2015-02-12

* Changed dependency to use XP ~6.0 (instead of dev-master) - @thekid

## 1.3.2, 2015-01-30

* Fixed bug in `ResourcesIn` template loader which loads templates
  from the class path - see pull request #4 (@kiesel, @thekid)

## 1.3.1, 2014-12-31

* Published on packagist.org - @thekid

## 1.3.0, 2014-09-23

* Made it possible to optionally pass an array of file extensions to
  the `FilesIn` and `ResourcesIn` template loader, defaulting to 
  ".mustache" - @thekid
* Added possibility to precompile templates and use them multiple
  times later on. See pull request #2 - @thekid
* Added support for options to sections and variables, which are not
  part of the specification but make subclassing easier - e.g. for
  implementing [HandleBars](http://handlebarsjs.com/) templates. See
  pull request #1 - @thekid
* Added support for "length" pseudo-member on arrays. - @thekid
* Fixed parent context lookups - @thekid
* Added ability to use objects as helpers. Their instance methods will
  be called with the current node and context - just like the helper
  closures. - @thekid
* Added possibility to extend / overwrite parser's tag interpretation
  by using `MustacheParser::withHandler()` - @thekid
* Added support for `../` selector to access parent context and `./`
  for explicitely using local context - @thekid
* Made Context implementation easier by moving data interpretation
  and conversion rules there from the VariableNode and SectionNode's
  `evaluate()` methods - @thekid

## 1.2.0, 2013-12-10

* Added `ResourcesIn` loader to load templates from the class path
  @thekid
* Added support for context data with array overloading via PHP's 
  `ArrayAccess` interface - @thekid
* Moved tests to com.github.mustache.*unittest* package - @thekid
* Make `Context` an abstract base class and add an implementation 
  called `DataContext` - @thekid

## 1.1.0, 2013-05-10

* Make sourcecode compliant with [new XP coding guidelines](https://github.com/xp-framework/rfc/issues/208) - @thekid
* Unbundle unittest parameterization implementation, it is now in the
  XP Framework's 5.9.1-RELEASE- @thekid

## 1.0.0, 2013-04-09

* Fulfill current specification to 100%- @thekid
* Add MustacheEngine::compile() method to compile templates- @thekid
* Also support invokeable objects as closures- @thekid
* Implement helpers. Think of helpers as "omnipresent" context- @thekid
* Detect illegal nesting and unclosed sections- @thekid
* Refactor: Extract parser from engine class- @thekid

## 0.9.0, 2013-04-07

* Initial release supporting all non-optional features of the Mustache 
  spec and the optional lambda feature- @thekid