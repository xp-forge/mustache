<?php namespace com\github\mustache\unittest;

use com\github\mustache\FileBasedTemplateLoader;
use io\streams\MemoryInputStream;

class FileBasedTemplateLoaderTest extends \unittest\TestCase {

  /**
   * Returns a new FileBasedTemplateLoader instance
   *
   * @param  var[] args constructor arguments
   * @return com.github.mustache.FileBasedTemplateLoader
   */
  protected function newFixture($args) {
    return newinstance(FileBasedTemplateLoader::class, $args, [
      'askedFor' => [],
      'variantsOf' => function($name) {
        return array_merge(parent::variantsOf($name), ['test']);
      },
      'inputStreamFor' => function($name) {
        if ('test' === $name) {
          return new MemoryInputStream('test');
        } else {
          $this->askedFor[]= $name;
          return null;
        }
      },
      'entries' => function() {
        return function($package) {
          if ('' === rtrim($package, '/')) {
            return ['test'];
          } else {
            return [];
          }
        };
      }
    ]);
  }

  #[@test]
  public function load_asks_for_mustache_extension_by_default() {
    $loader= $this->newFixture(['base']);
    $loader->source('template');
    $this->assertEquals(['template.mustache'], $loader->askedFor);
  }

  #[@test]
  public function load_asks_for_all_given_variants() {
    $loader= $this->newFixture(['base', ['.mustache', '.ms']]);
    $loader->source('template');
    $this->assertEquals(['template.mustache', 'template.ms'], $loader->askedFor);
  }

  #[@test]
  public function templates_in_root() {
    $loader= $this->newFixture(['base']);
    $this->assertEquals(['test'], $loader->listing()->templates());
  }

  #[@test, @values([null, '/'])]
  public function templates_in_root_explicitely($root) {
    $loader= $this->newFixture(['base']);
    $this->assertEquals(['test'], $loader->listing()->package($root)->templates());
  }

  #[@test, @values(['partials', 'partials/'])]
  public function templates_in_package($package) {
    $loader= $this->newFixture(['base']);
    $this->assertEquals([], $loader->listing()->package($package)->templates());
  }
}