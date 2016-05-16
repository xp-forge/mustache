<?php namespace com\github\mustache\unittest;

use com\github\mustache\InMemory;
use com\github\mustache\TemplateNotFoundException;
use io\streams\Streams;

class InMemoryTest extends \unittest\TestCase {

  #[@test]
  public function load() {
    $content= 'Mustache template {{id}}';
    $loader= new InMemory(['test' => $content]);
    $this->assertEquals($content, Streams::readAll($loader->load('test')));
  }

  #[@test, @expect(TemplateNotFoundException::class)]
  public function load_non_existant() {
    (new InMemory())->load('@non.existant@');
  }

  #[@test]
  public function templates_in_root() {
    $loader= new InMemory(['navigation' => 'Test']);
    $this->assertEquals(['navigation'], $loader->templatesIn());
  }

  #[@test, @values([null, '/'])]
  public function templates_in_root_explicitely($root) {
    $loader= new InMemory(['navigation' => 'Test']);
    $this->assertEquals(['navigation'], $loader->templatesIn($root));
  }

  #[@test, @values(['partials', 'partials/'])]
  public function templates_in_package($package) {
    $loader= new InMemory(['partials/navigation' => 'Test']);
    $this->assertEquals(['partials/navigation'], $loader->templatesIn($package));
  }

  #[@test, @values([null, '/'])]
  public function templates_not_fetched_recursively_from_root($root) {
    $loader= new InMemory([
      'navigation'        => 'Global',
      'navigation/header' => 'Header',
      'navigation/aside'  => 'Aside'
    ]);
    $this->assertEquals(['navigation'], $loader->templatesIn($root));
  }

  #[@test, @values(['partials', 'partials/'])]
  public function templates_not_fetched_recursively_from_package($package) {
    $loader= new InMemory([
      'partials/navigation'        => 'Global',
      'partials/navigation/header' => 'Header',
      'partials/navigation/aside'  => 'Aside'
    ]);
    $this->assertEquals(['partials/navigation'], $loader->templatesIn($package));
  }
}