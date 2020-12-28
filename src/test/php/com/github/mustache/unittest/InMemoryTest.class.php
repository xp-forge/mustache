<?php namespace com\github\mustache\unittest;

use com\github\mustache\InMemory;
use unittest\{Assert, Test, Values};

class InMemoryTest {

  #[Test]
  public function source() {
    $content= 'Mustache template {{id}}';
    $loader= new InMemory(['test' => $content]);
    Assert::equals($content, $loader->source('test')->code());
  }

  #[Test]
  public function source_non_existant() {
    Assert::false((new InMemory())->source('@non.existant@')->exists());
  }

  #[Test]
  public function templates_in_root() {
    $loader= new InMemory(['navigation' => 'Test']);
    Assert::equals(['navigation'], $loader->listing()->templates());
  }

  #[Test]
  public function packages_in_packages() {
    $loader= new InMemory(['partials/navigation' => 'Test']);
    Assert::equals(['partials/'], $loader->listing()->packages());
  }

  #[Test]
  public function packages_in_packages_not_fetched_recursively() {
    $loader= new InMemory([
      'partials/navigation/header' => 'Header',
      'partials/navigation/aside'  => 'Aside',
      'partials/content/main'      => 'Body'
    ]);
    Assert::equals(['partials/'], $loader->listing()->packages());
  }

  #[Test, Values([null, '/'])]
  public function templates_in_root_explicitely($root) {
    $loader= new InMemory(['navigation' => 'Test']);
    Assert::equals(['navigation'], $loader->listing()->package($root)->templates());
  }

  #[Test, Values(['partials', 'partials/'])]
  public function templates_in_package($package) {
    $loader= new InMemory(['partials/navigation' => 'Test']);
    Assert::equals(['partials/navigation'], $loader->listing()->package($package)->templates());
  }

  #[Test, Values([null, '/'])]
  public function templates_not_fetched_recursively_from_root($root) {
    $loader= new InMemory([
      'navigation'        => 'Global',
      'navigation/header' => 'Header',
      'navigation/aside'  => 'Aside'
    ]);
    Assert::equals(['navigation'], $loader->listing()->package($root)->templates());
  }

  #[Test, Values(['partials', 'partials/'])]
  public function templates_not_fetched_recursively_from_package($package) {
    $loader= new InMemory([
      'partials/navigation'        => 'Global',
      'partials/navigation/header' => 'Header',
      'partials/navigation/aside'  => 'Aside'
    ]);
    Assert::equals(['partials/navigation'], $loader->listing()->package($package)->templates());
  }

  #[Test]
  public function issue_10() {
    $content= 'Mustache template #1 {{id}}';
    $loader= new InMemory(['test' => $content]);
    Assert::equals($content, $loader->source('test')->code());
  }
}