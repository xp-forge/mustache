<?php
  namespace com\github\mustache;

  use \io\Folder;
  use \io\File;
  use \io\FileUtil;

  /**
   * File-based template loading loads templates from the file system.
   */
  class FilesIn extends \lang\Object implements TemplateLoader {
    protected $base;

    /**
     * Creates a new file-based template loader
     *
     * @param var $base The base folder, either an io.Folder or a string
     */
    public function __construct($arg) {
      if ($arg instanceof Folder) {
        $this->base= $arg;
      } else {
        $this->base= new Folder($arg);
      }
    }

  	/**
  	 * Load a template by a given name
  	 *
  	 * @param  string $name The template name, including the ".mustache" extension
  	 * @return string The bytes
  	 * @throws com.github.mustache.TemplateNotFoundException
  	 */
    public function load($name) {
      $template= new File($this->base, $name);
      if (!$template->exists()) {
        throw new TemplateNotFoundException('Cannot find template '.$name.' in '.$this->base->getURI());
      }
      return FileUtil::getContents($template);
    }
  }
?>