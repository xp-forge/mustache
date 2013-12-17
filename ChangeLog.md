Mustache for XP Framework ChangeLog
===================================

?.?.?, ??.??.????
-----------------
* Added ability to use objects as helpers. Their instance methods will
  be called with the current node and context - just like the helper
  closures. - @thekid
* Added possibility to extend / overwrite parser's tag interpretation
  by using `MustacheParser::withHandler()` - @thekid
* Added support for `../` selector to access parent context - @thekid
* Made Context implementation easier by moving data interpretation
  and conversion rules there from the VariableNode and SectionNode's
  `evaluate()` methods - @thekid

1.2.0, 10.12.2013
-----------------
* Added `ResourcesIn` loader to load templates from the class path
  @thekid
* Added support for context data with array overloading via PHP's 
  `ArrayAccess` interface - @thekid
* Moved tests to com.github.mustache.*unittest* package - @thekid
* Make `Context` an abstract base class and add an implementation 
  called `DataContext` - @thekid

1.1.0, 10.05.2013
-----------------
* Make sourcecode compliant with [new XP coding guidelines](https://github.com/xp-framework/rfc/issues/208) - @thekid
* Unbundle unittest parameterization implementation, it is now in the
  XP Framework's 5.9.1-RELEASE- @thekid

1.0.0, 09.04.2013
-----------------
* Fulfill current specification to 100%- @thekid
* Add MustacheEngine::compile() method to compile templates- @thekid
* Also support invokeable objects as closures- @thekid
* Implement helpers. Think of helpers as "omnipresent" context- @thekid
* Detect illegal nesting and unclosed sections- @thekid
* Refactor: Extract parser from engine class- @thekid

0.9.0, 07.04.2013
-----------------
* Initial release supporting all non-optional features of the Mustache 
  spec and the optional lambda feature- @thekid