<?php
  namespace com\github\mustache;

  use \io\collections\iterate\ExtensionEqualsFilter;
  use \io\collections\iterate\FilteredIOCollectionIterator;
  use \io\collections\FileCollection;
  use \webservices\json\JsonFactory;
  use \lang\Runtime;
  use \util\profiling\Timer;

  /**
   * Runs the Mustache specs
   *
   * @see https://github.com/mustache/spec
   */
  class RunSpec extends \util\cmd\Command {
    protected $base;
    protected $files;
    protected $verbose= FALSE;
    protected $stop= FALSE;

    /**
     * Sets targets to run
     */
    #[@arg(position = 0)]
    public function setTarget($target) {
      if (is_file($target)) {
        $this->base= new FileCollection(dirname($target));
        $this->files= array($this->base->getElement(basename($target)));
      } else {
        $this->base= new FileCollection($target);
        $this->files= new FilteredIOCollectionIterator($this->base, new ExtensionEqualsFilter('json'));
      }
    }

    /**
     * Show verbose output
     */
    #[@arg]
    public function setVerbose() {
      $this->verbose= TRUE;
    }

    /**
     * Stop after the first failed test
     */
    #[@arg]
    public function setStop() {
      $this->stop= TRUE;
    }

    public function run() {
      $json= JsonFactory::create();
      $templates= newinstance(\lang\XPClass::forName('com.github.mustache.TemplateLoader')->getName(), array(), '{
        protected $templates= array();

        public function set($partials) {
          $this->templates= array();
          foreach ($partials as $name => $data) {
            $this->templates[$name.".mustache"]= $data;
          }
        }

        public function load($name) {
          if (!isset($this->templates[$name])) {
            throw new TemplateNotFoundException($name);
          }
          return $this->templates[$name];
        }
      }');
      $engine= create(new MustacheEngine())->withTemplates($templates);
      $timer= new Timer();
      $failures= array();
      $failed= $succeeded= 0;
      $total= 1;
      $time= 0.0;

      // Execute all tests
      $this->out->write('[');
      foreach ($this->files as $file) {
        $spec= $json->decodeFrom($file->getInputStream());

        foreach ($spec['tests'] as $test) {
          if (0 === ($total++ % 72)) $this->out->writeLine();
          $templates->set(isset($test['partials']) ? $test['partials'] : array());

          $timer->start();
          try {
            $result= $engine->render($test['template'], $test['data']);
            if ($result !== $test['expected']) {
              $this->out->write('F');
              $failures[]= new Failure($test, $result);
              $failed++;
            } else {
              $this->out->write('.');
              $succeeded++;
            }
          } catch (\lang\Throwable $e) {
            $this->out->write('E');
            $failures[]= new Failure($test, $e);
            $failed++;
          }
          $timer->stop();
          $time+= $timer->elapsedTime();

          if ($this->stop && $failed) break 2;
        }
      }
      $this->out->writeLine(']');
      $this->out->writeLine();

      // Wrap up results
      $this->out->writeLinef(
        "%s: %d run, %d succeeded, %d failed\nMemory used: %.3f kB\nTime taken: %.3f seconds", 
        $failed ? 'FAIL' : 'OK',
        $succeeded + $failed,
        $succeeded,
        $failed,
        Runtime::getInstance()->memoryUsage() / 1024,
        $time
      );
      $this->verbose && $this->out->writeLine($failures);
    }
  }
?>