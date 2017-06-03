<?php namespace com\github\mustache;

use text\Tokenizer;

/**
 * Abstract base class for mustache template parsing. Inherit and 
 * implement the `initialize()` method to install handlers for the
 * specific tags.
 *
 * ## Handlers
 * A handler takes the following form:
 * 
 * ```php
 * $handler= function($tag, $state[, $parse]) {
 * }
 * ```
 *
 * - The tag variable contains the complete tag, that is, everything
 *   between the start and end tokens (typically `{{` and `}}`).
 * - The parse state is passed along. See the ParseState class for 
 *   details. All members are read/write
 * - Optionally, the parse parameter can be used to access the parser
 *   instance.
 *
 * The handler may return a number of characters to forward inside
 * the current line, negative or positive.
 */
abstract class AbstractMustacheParser implements TemplateParser {
  protected $handlers= [];
  protected $standalone= [];
  protected $options;

  /**
   * Perform initialization.
   */
  public function __construct() {
    $this->initialize();
  }

  /**
   * Initialize this parser
   *
   * @return void
   */
  protected abstract function initialize();

  /**
   * Add a handler
   *
   * @param  string $token Token characters to react on; use NULL to set the default handler
   * @param  bool $standalone Whether these tags should be standalone on a line by itself
   * @param  var $handler A function
   * @return self
   */
  public function withHandler($tokens, $standalone, $handler) {
    if (null === $tokens) {
      $this->handlers[null]= $handler;
    } else for ($i= 0; $i < strlen($tokens); $i++) {
      $this->handlers[$tokens{$i}]= $handler;
      $standalone && $this->standalone[$tokens{$i}]= true;
    }
    return $this;
  }

  /**
   * Parse a template
   *
   * @param  string $template The template as a string
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent What to prefix before each line
   * @return com.github.mustache.Node The parsed template
   * @throws com.github.mustache.TemplateFormatException
   */
  public function parse(Tokenizer $tokens, $start= '{{', $end= '}}', $indent= '') {
    $state= new ParseState();
    $state->target= new NodeList();
    $state->start= $start;
    $state->end= $end;
    $state->parents= [];
    $standalone= implode('', array_keys($this->standalone));

    // Tokenize template
    $tokens->delimiters= "\n";
    $tokens->returnDelims= true;
    while ($tokens->hasMoreTokens()) {
      $token= $tokens->nextToken();

      // Yield empty lines as separate text nodes
      if ("\n" === $token) {
        $state->target->add(new TextNode($indent.$token));
        continue;
      }

      $line= $indent.$token.$tokens->nextToken();
      $offset= 0;
      do {

        // Parse line
        $state->padding= '';
        if (false === ($s= strpos($line, $state->start, $offset))) {
          $text= substr($line, $offset);
          $tag= null;
          $offset= strlen($line);
        } else {
          while (false === ($e= strpos($line, $state->end, $s+ strlen($state->start)))) {
            if (!$tokens->hasMoreTokens()) {
              throw new TemplateFormatException('Unclosed '.$state->start.', expecting '.$state->end);
            }
            $line.= $indent.$tokens->nextToken().$tokens->nextToken();
          }
          $text= substr($line, $offset, $s- $offset);
          $tag= substr($line, $s+ strlen($state->start), $e- $s- strlen($state->end));
          $offset= $e + strlen($state->end);

          // Check for standalone tags on a line by themselves
          if (0 === strcspn($tag, $standalone)) {
            if ('' === trim(substr($line, 0, $s).substr($line, $offset))) {
              $offset= strlen($line);
              $state->padding= substr($line, 0, $s);
              $text= '';
            }
          }
        }

        // Handle text
        if ('' !== $text) {
          $state->target->add(new TextNode($text));
        }

        // Handle tag
        if (null === $tag) {
          continue;
        } else if (isset($this->handlers[$tag{0}])) {
          $f= $this->handlers[$tag{0}];
        } else {
          $f= $this->handlers[null];
        }
        $offset+= $f($tag, $state, $this);
      } while ($offset < strlen($line));
    }

    // Check for unclosed sections
    if (!empty($state->parents)) {
      throw new TemplateFormatException('Unclosed section '.$state->target->name());
    }

    // \util\cmd\Console::writeLine($state->target);
    return $state->target;
  }
}