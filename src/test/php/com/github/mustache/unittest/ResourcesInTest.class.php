<?php namespace com\github\mustache\unittest;

use com\github\mustache\ResourcesIn;
use lang\ClassLoader;
use unittest\{Assert, Test, Values};

class ResourcesInTest {

  #[Test]
  public function source_from_default_class_loader() {
    $loader= new ResourcesIn(ClassLoader::getDefault());
    Assert::equals(
      'Mustache template {{id}}',
      $loader->source('com/github/mustache/unittest/template')->code()
    );
  }

  #[Test]
  public function source_non_existant() {
    Assert::false((new ResourcesIn(ClassLoader::getDefault()))->source('@non.existant@')->exists());
  }

  #[Test]
  public function templates_in_root() {
    $loader= new ResourcesIn(ClassLoader::getDefault());
    Assert::equals([], $loader->listing()->templates());
  }

  #[Test, Values([null, '/'])]
  public function templates_in_root_explicitely($root) {
    $loader= new ResourcesIn(ClassLoader::getDefault());
    Assert::equals([], $loader->listing()->package($root)->templates());
  }

  #[Test, Values(['com/github/mustache/unittest', 'com/github/mustache/unittest/'])]
  public function templates_in_package($package) {
    $loader= new ResourcesIn(ClassLoader::getDefault());
    Assert::equals(['com/github/mustache/unittest/template'], $loader->listing()->package($package)->templates());
  }

  #[Test]
  public function packages_in_root() {
    $loader= new ResourcesIn(typeof($this)->getClassLoader());
    Assert::equals(['com/'], $loader->listing()->packages());
  }

  #[Test, Values(['com/github/mustache', 'com/github/mustache/'])]
  public function packages_in_package($package) {
    $loader= new ResourcesIn(typeof($this)->getClassLoader());
    Assert::equals(['com/github/mustache/unittest/'], $loader->listing()->package($package)->packages());
  }
}