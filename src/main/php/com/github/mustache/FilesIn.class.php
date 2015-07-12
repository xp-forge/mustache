<?php namespace com\github\mustache;

use io\Folder;
use io\File;

/**
 * File-based template loading loads templates from a given folder
 */
class FilesIn extends FileBasedTemplateLoader {

  /**
   * Creates a new file-based template loader
   *
   * @param var $base The base folder, either an io.Folder or a string
   * @param string[] $extensions File extensions to check, including leading "."
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
    return $f->exists() ? $f->getInputStream() : null;
  }
}