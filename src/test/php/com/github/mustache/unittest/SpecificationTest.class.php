<?php namespace com\github\mustache\unittest;

use com\github\mustache\{InMemory, MustacheEngine};
use io\collections\iterate\{ExtensionEqualsFilter, FilteredIOCollectionIterator};
use io\collections\{FileCollection, FileElement};
use test\{Assert, Test, TestCase, Values};
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
class SpecificationTest {
  private $target;

  /**
   * Constructor
   *
   * @param  string $target The directory in which the spec files exist
   */
  public function __construct($target= null) {
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

  #[Test, Values(from: 'specifications')]
  public function specification_met($name, $test) {

    // Select correct lambda
    if (isset($test['data']['lambda'])) {
      $test['data']['lambda']= fn($text, $context) => eval($test['data']['lambda']['php']);
    }

    // Render, and assert result
    Assert::equals($test['expected'], (new MustacheEngine())
      ->withTemplates(new InMemory(isset($test['partials']) ? $test['partials'] : []))
      ->render($test['template'], $test['data'])
    );
  }
}