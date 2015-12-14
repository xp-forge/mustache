<?php namespace com\github\mustache\unittest;

use com\github\mustache\FileBasedTemplateLoader;

class FileBasedTemplateLoaderTest extends \unittest\TestCase {

  /**
   * Returns a new FileBasedTemplateLoader instance
   *
   * @param  var[] args constructor arguments
   * @return com.github.mustache.FileBasedTemplateLoader
   */
  protected function newFixture($args) {
    return newinstance(FileBasedTemplateLoader::class, $args, '{
      public $askedFor= [];

      protected function variantsOf($name) {
        return array_merge(parent::variantsOf($name), ["test"]);
      }

      protected function inputStreamFor($name) {
        if ("test" === $name) {
          return new \io\streams\MemoryInputStream("test");
        } else {
          $this->askedFor[]= $name;
          return null;
        }
      }
    }');
  }

  #[@test]
  public function load_asks_for_mustache_extension_by_default() {
    $loader= $this->newFixture(['base']);
    $loader->load('template');
    $this->assertEquals(['template.mustache'], $loader->askedFor);
  }

  #[@test]
  public function load_asks_for_all_given_variants() {
    $loader= $this->newFixture(['base', ['.mustache', '.ms']]);
    $loader->load('template');
    $this->assertEquals(['template.mustache', 'template.ms'], $loader->askedFor);
  }
}