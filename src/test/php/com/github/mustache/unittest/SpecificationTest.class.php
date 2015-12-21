<?php namespace com\github\mustache\unittest;

use com\github\mustache\MustacheEngine;
use com\github\mustache\InMemory;
use io\collections\iterate\ExtensionEqualsFilter;
use io\collections\iterate\FilteredIOCollectionIterator;
use io\collections\FileCollection;
use io\collections\FileElement;
use text\json\StreamInput;

/**
 * Executes the Mustache specifications
 *
 * To run, use:
 * ```sh
 * $ wget 'https://github.com/mustache/spec/archive/master.zip' -O master.zip
 * $ unzip master.zip && rm master.zip
 * $ unittest com.github.mustache.unittest.SpecificationTest -a spec-master/specs
 * ```
 *
 * @see https://github.com/mustache/spec
 */
class SpecificationTest extends \unittest\TestCase {
  protected $target= null;

  /**
   * Constructor
   *
   * @param string $name
   * @param string $target The directory in which the spec files exist
   */
  public function __construct($name, $target= null) {
    parent::__construct($name);
    $this->target= $target;
  }

  /** @return php.Iterator */
  public function specifications() {
    if (is_file($this->target)) {
      $files= [new FileElement($this->target)];
    } else {
      $files= new FilteredIOCollectionIterator(new FileCollection($this->target), new ExtensionEqualsFilter('json'));
    }

    // Return an array of argument lists to be passed to specification
    foreach ($files as $file) {
      $spec= (new StreamInput($file->in()))->read();
      if (isset($spec['tests'])) {
        foreach ($spec['tests'] as $test) {
          yield [$test['name'], $test];
        }
      }
    }
  }

  #[@test, @values('specifications')]
  public function specification_met($name, $test) {

    // Select correct lambda
    if (isset($test['data']['lambda'])) {
      $php= $test['data']['lambda']['php'];
      $test['data']['lambda']= function($text, $context) use($php) {
        return eval($php);
      };
    }

    // Render, and assert result
    $this->assertEquals($test['expected'], (new MustacheEngine())
      ->withTemplates(new InMemory(isset($test['partials']) ? $test['partials'] : []))
      ->render($test['template'], $test['data'])
    );
  }
}