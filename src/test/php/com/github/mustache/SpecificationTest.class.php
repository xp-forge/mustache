<?php
  namespace com\github\mustache;

  use \io\collections\iterate\ExtensionEqualsFilter;
  use \io\collections\iterate\FilteredIOCollectionIterator;
  use \io\collections\FileCollection;
  use \io\collections\FileElement;
  use \webservices\json\JsonFactory;

  class SpecificationTest extends \unittest\TestCase {
    protected $target= NULL;

    public function __construct($name, $target= NULL) {
      parent::__construct($name);
      $this->target= $target;
    }

    public function specifications() {
      if (is_file($this->target)) {
        $files= array(new FileElement($this->target));
      } else {
        $files= new FilteredIOCollectionIterator(new FileCollection($this->target), new ExtensionEqualsFilter('json'));
      }

      // Return an array of argument lists to be passed to specification
      $r= array();
      $json= JsonFactory::create();
      foreach ($files as $file) {
        $spec= $json->decodeFrom($file->getInputStream());
        foreach ($spec['tests'] as $test) {
          $r[]= array($test['name'], $test);
        }
      }
      return $r;
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
      $this->assertEquals($test['expected'], create(new MustacheEngine())
        ->withTemplates(new InMemory(isset($test['partials']) ? $test['partials'] : array()))
        ->render($test['template'], $test['data'])
      );
    }
  }
?>