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
    return newinstance('com.github.mustache.FileBasedTemplateLoader', $args, '{
      public $askedFor= array();

      protected function variantsOf($name) {
        return array_merge(parent::variantsOf($name), array("test"));
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
    $loader= $this->newFixture(array('base'));
    $loader->load('template');
    $this->assertEquals(array('template.mustache'), $loader->askedFor);
  }

  #[@test]
  public function load_asks_for_all_given_variants() {
    $loader= $this->newFixture(array('base', array('.mustache', '.ms')));
    $loader->load('template');
    $this->assertEquals(array('template.mustache', 'template.ms'), $loader->askedFor);
  }
}