<?php namespace com\github\mustache\unittest;

use com\github\mustache\ResourcesIn;
use lang\ClassLoader;

class ResourcesInTest extends \unittest\TestCase {

  #[@test]
  public function source_from_default_class_loader() {
    $loader= new ResourcesIn(ClassLoader::getDefault());
    $this->assertEquals(
      'Mustache template {{id}}',
      $loader->source('com/github/mustache/unittest/template')->code()
    );
  }

  #[@test]
  public function source_non_existant() {
    $this->assertFalse((new ResourcesIn(ClassLoader::getDefault()))->source('@non.existant@')->exists());
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

  #[@test]
  public function packages_in_root() {
    $loader= new ResourcesIn(typeof($this)->getClassLoader());
    $this->assertEquals(['com/'], $loader->listing()->packages());
  }

  #[@test, @values(['com/github/mustache', 'com/github/mustache/'])]
  public function packages_in_package($package) {
    $loader= new ResourcesIn(typeof($this)->getClassLoader());
    $this->assertEquals(['com/github/mustache/unittest/'], $loader->listing()->package($package)->packages());
  }
}