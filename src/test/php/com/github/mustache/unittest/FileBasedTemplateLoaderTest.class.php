<?php namespace com\github\mustache\unittest;

use com\github\mustache\FileBasedTemplateLoader;
use io\streams\MemoryInputStream;
use unittest\{Test, Values};

class FileBasedTemplateLoaderTest extends \unittest\TestCase {

  /**
   * Returns a new FileBasedTemplateLoader instance
   *
   * @param  var[] args constructor arguments
   * @return com.github.mustache.FileBasedTemplateLoader
   */
  protected function newFixture($args) {
    return new class(...$args) extends FileBasedTemplateLoader {
      public $askedFor= [];

      public function variantsOf($name) {
        return array_merge(parent::variantsOf($name), ['test']);
      }

      public function inputStreamFor($name) {
        if ('test' === $name) {
          return new MemoryInputStream('test');
        } else {
          $this->askedFor[]= $name;
          return null;
        }
      }

      public function entries() {
        return function($package) {
          if ('' === rtrim($package, '/')) {
            return ['test'];
          } else {
            return [];
          }
        };
      }
    };
  }

  #[Test]
  public function load_asks_for_mustache_extension_by_default() {
    $loader= $this->newFixture(['base']);
    $loader->source('template');
    $this->assertEquals(['template.mustache'], $loader->askedFor);
  }

  #[Test]
  public function load_asks_for_all_given_variants() {
    $loader= $this->newFixture(['base', ['.mustache', '.ms']]);
    $loader->source('template');
    $this->assertEquals(['template.mustache', 'template.ms'], $loader->askedFor);
  }

  #[Test]
  public function templates_in_root() {
    $loader= $this->newFixture(['base']);
    $this->assertEquals(['test'], $loader->listing()->templates());
  }

  #[Test, Values([null, '/'])]
  public function templates_in_root_explicitely($root) {
    $loader= $this->newFixture(['base']);
    $this->assertEquals(['test'], $loader->listing()->package($root)->templates());
  }

  #[Test, Values(['partials', 'partials/'])]
  public function templates_in_package($package) {
    $loader= $this->newFixture(['base']);
    $this->assertEquals([], $loader->listing()->package($package)->templates());
  }
}