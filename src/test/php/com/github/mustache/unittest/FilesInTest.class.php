<?php namespace com\github\mustache\unittest;

use com\github\mustache\{FilesIn, TemplateNotFoundException};
use io\{File, FileUtil, Folder};
use lang\System;
use unittest\{Assert, AfterClass, BeforeClass, Test, Values};

class FilesInTest {
  private $temp;

  #[Before]
  public function createFiles() {
    $this->temp= new Folder(System::tempDir(), uniqid(microtime(true)));
    $this->temp->create();

    $partials= new Folder($this->temp, 'partials');
    $partials->create();

    FileUtil::setContents(new File($this->temp, 'test.mustache'), 'Mustache template {{id}}');
    FileUtil::setContents(new File($partials, 'navigation.mustache'), '{{#if nav}}nav{{/if}}');
  }

  #[After]
  public function cleanupFiles() {
    $this->temp->unlink();
  }

  #[Test]
  public function source_from_default_class_loader() {
    $loader= new FilesIn($this->temp);
    Assert::equals(
      'Mustache template {{id}}',
      $loader->source('test')->code()
    );
  }

  #[Test]
  public function source_non_existant() {
    Assert::false((new FilesIn($this->temp))->source('@non.existant@')->exists());
  }

  #[Test]
  public function templates_in_root() {
    $loader= new FilesIn($this->temp);
    Assert::equals(['test'], $loader->listing()->templates());
  }

  #[Test]
  public function packages_in_root() {
    $loader= new FilesIn($this->temp);
    Assert::equals(['partials/'], $loader->listing()->packages());
  }

  #[Test, Values(['partials', 'partials/'])]
  public function packages_in_package($package) {
    $loader= new FilesIn($this->temp);
    Assert::equals([], $loader->listing()->package($package)->packages());
  }

  #[Test, Values(['partials', 'partials/'])]
  public function templates_in_package($package) {
    $loader= new FilesIn($this->temp);
    Assert::equals(['partials/navigation'], $loader->listing()->package($package)->templates());
  }
}