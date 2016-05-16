<?php namespace com\github\mustache;

use io\Folder;
use io\File;

/**
 * File-based template loading loads templates from a given folder
 *
 * @test  xp://com.github.mustache.unittest.FilesInTest
 */
class FilesIn extends FileBasedTemplateLoader {

  /**
   * Creates a new file-based template loader
   *
   * @param  string|io.Folder $base The base folder
   * @param  string[] $extensions File extensions to check, including leading "."
   */
  public function __construct($arg, $extensions= ['.mustache']) {
    parent::__construct(
      $arg instanceof Folder ? $arg : new Folder($arg),
      $extensions
    );
  }

  /**
   * Returns an inputstream for a given name, or NULL
   *
   * @param  string $name
   * @return io.streams.InputStream
   */
  protected function inputStreamFor($name) {
    $f= new File($this->base, $name);
    return $f->exists() ? $f->in() : null;
  }

  /**
   * Returns available templates
   *
   * @param   string $namespace Optional, omit for root namespace
   * @return  string[]
   */
  public function templatesIn($namespace= null) {
    $namespace= rtrim($namespace, '/');
    if ('' === $namespace) {
      $folder= $this->base;
      $prefix= '';
    } else {
      $folder= new Folder($this->base, strtr($namespace, '/', DIRECTORY_SEPARATOR));
      $prefix= $namespace.'/';
    }

    $r= [];
    while ($entry= $folder->getEntry()) {
      foreach ($this->extensions as $extension) {
        $offset= -strlen($extension);
        if (0 === substr_compare($entry, $extension, $offset)) {
          $r[]= $prefix.substr($entry, 0, $offset);
        }
      }
    }
    $folder->rewind();
    return $r;
  }
}