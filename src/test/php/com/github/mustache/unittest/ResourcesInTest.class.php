<?php namespace com\github\mustache\unittest;

use com\github\mustache\ResourcesIn;
use com\github\mustache\TemplateNotFoundException;
use lang\ClassLoader;
use io\streams\Streams;

class ResourcesInTest extends \unittest\TestCase {

  #[@test]
  public function load_from_default_class_loader() {
    $loader= new ResourcesIn(ClassLoader::getDefault());
    $this->assertEquals(
      'Mustache template {{id}}',
      Streams::readAll($loader->load('com/github/mustache/unittest/template'))
    );
  }

  #[@test, @expect(TemplateNotFoundException::class)]
  public function load_non_existant() {
    (new ResourcesIn(ClassLoader::getDefault()))->load('@non.existant@');
  }

  #[@test]
  public function templates_in_root() {
    $loader= new ResourcesIn(ClassLoader::getDefault());
    $this->assertEquals([], $loader->listing()->templates());
  }

  #[@test, @values([null, '/'])]
  public function templates_in_root_explicitely($root) {
    $loader= new ResourcesIn(ClassLoader::getDefault());
    $this->assertEquals([], $loader->listing()->package($root)->templates());
  }

  #[@test, @values(['com/github/mustache/unittest', 'com/github/mustache/unittest/'])]
  public function templates_in_package($package) {
    $loader= new ResourcesIn(ClassLoader::getDefault());
    $this->assertEquals(['com/github/mustache/unittest/template'], $loader->listing()->package($package)->templates());
  }
}