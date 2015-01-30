<?php namespace com\github\mustache\unittest;

use com\github\mustache\ResourcesIn;
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
}