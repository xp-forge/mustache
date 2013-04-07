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
    protected $files;
    protected $verbose= FALSE;

    #[@arg(position = 0)]
    public function setBase($base) {
      $this->base= new FileCollection($base);
    }

    #[@arg]
    public function setVerbose() {
      $this->verbose= TRUE;
    }

    public function run() {
      $json= JsonFactory::create();
      $engine= create(new MustacheEngine())->withTemplates(new FilesIn($this->base->getURI()));
      $timer= new Timer();
      $failures= array();
      $failed= $succeeded= 0;
      $total= 1;
      $time= 0.0;

      // Execute all tests
      $this->out->write('[');
      foreach (new FilteredIOCollectionIterator($this->base, new ExtensionEqualsFilter('json')) as $file) {
        $spec= $json->decodeFrom($file->getInputStream());

        $timer->start();
        foreach ($spec['tests'] as $test) {
          if (0 === ($total++ % 72)) $this->out->writeLine();
          try {
            $result= $engine->render($test['template'], $test['data']);
            if ($result !== $test['expected']) {
              $this->out->write('F');
              $failures[]= array($test, $result);
              $failed++;
            } else {
              $this->out->write('.');
              $succeed++;
            }
          } catch (\lang\Throwable $e) {
            $this->out->write('E');
            $failures[]= array($test, $e);
            $failed++;
          }
        }
        $timer->stop();
        $time+= $timer->elapsedTime();
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