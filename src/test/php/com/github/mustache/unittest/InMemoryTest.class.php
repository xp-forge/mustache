<?php namespace com\github\mustache\unittest;

use com\github\mustache\InMemory;

class InMemoryTest extends \unittest\TestCase {

  #[@test]
  public function load() {
    $content= 'Mustache template {{id}}';
    $loader= new InMemory(['test' => $content]);
    $this->assertEquals($content, $loader->load('test')->tokens()->nextToken("\n"));
  }

  #[@test]
  public function load_non_existant() {
    $this->assertFalse((new InMemory())->load('@non.existant@')->exists());
  }

  #[@test]
  public function templates_in_root() {
    $loader= new InMemory(['navigation' => 'Test']);
    $this->assertEquals(['navigation'], $loader->listing()->templates());
  }

  #[@test]
  public function packages_in_packages() {
    $loader= new InMemory(['partials/navigation' => 'Test']);
    $this->assertEquals(['partials/'], $loader->listing()->packages());
  }

  #[@test]
  public function packages_in_packages_not_fetched_recursively() {
    $loader= new InMemory([
      'partials/navigation/header' => 'Header',
      'partials/navigation/aside'  => 'Aside',
      'partials/content/main'      => 'Body'
    ]);
    $this->assertEquals(['partials/'], $loader->listing()->packages());
  }

  #[@test, @values([null, '/'])]
  public function templates_in_root_explicitely($root) {
    $loader= new InMemory(['navigation' => 'Test']);
    $this->assertEquals(['navigation'], $loader->listing()->package($root)->templates());
  }

  #[@test, @values(['partials', 'partials/'])]
  public function templates_in_package($package) {
    $loader= new InMemory(['partials/navigation' => 'Test']);
    $this->assertEquals(['partials/navigation'], $loader->listing()->package($package)->templates());
  }

  #[@test, @values([null, '/'])]
  public function templates_not_fetched_recursively_from_root($root) {
    $loader= new InMemory([
      'navigation'        => 'Global',
      'navigation/header' => 'Header',
      'navigation/aside'  => 'Aside'
    ]);
    $this->assertEquals(['navigation'], $loader->listing()->package($root)->templates());
  }

  #[@test, @values(['partials', 'partials/'])]
  public function templates_not_fetched_recursively_from_package($package) {
    $loader= new InMemory([
      'partials/navigation'        => 'Global',
      'partials/navigation/header' => 'Header',
      'partials/navigation/aside'  => 'Aside'
    ]);
    $this->assertEquals(['partials/navigation'], $loader->listing()->package($package)->templates());
  }
}