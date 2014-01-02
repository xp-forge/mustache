<?php namespace com\github\mustache;

/**
 * Parses mustache templates
 *
 * @test  xp://com.github.mustache.unittest.ParsingTest
 */
class MustacheParser extends AbstractMustacheParser {

  /**
   * Tokenize name and options from a given tag, e.g.:
   * - 'tag' = ['tag']
   * - 'tag option "option 2"' = ['tag', 'option', 'option 2']
   *
   * @param  string $tag
   * @return string[]
   */
  public function options($tag) {
    $parsed= array();
    for ($o= 0, $l= strlen($tag); $o < $l; $o+= $p + 1) {
      if ('"' === $tag{$o}) {
        $p= strcspn($tag, '"', $o + 1) + 2;
        $parsed[]= substr($tag, $o + 1, $p - 2);
      } else {
        $p= strcspn($tag, ' ', $o);
        $parsed[]= substr($tag, $o, $p);
      }
    }
    return $parsed;
  }

  /**
   * Initialize this parser.
   */
  protected function initialize() {

    // Sections
    $this->withHandler('#^', true, function($tag, $state, $parse) {
      $parsed= $parse->options(trim(substr($tag, 1)));
      $state->parents[]= $state->target;
      $state->target= $state->target->add(new SectionNode(
        array_shift($parsed),
        '^' === $tag{0},
        $parsed,
        null,
        $state->start,
        $state->end
      ));
    });
    $this->withHandler('/', true, function($tag, $state) {
      $name= trim(substr($tag, 1));
      if ($name !== $state->target->name()) {
        throw new TemplateFormatException('Illegal nesting, expected /'.$state->target->name().', have /'.$name);
      }
      $state->target= array_pop($state->parents);
    });

    // > partial
    $this->withHandler('>', true, function($tag, $state) {
      $state->target->add(new PartialNode(trim(substr($tag, 1), ' '), $state->padding));
    });

    // ! ... for comments
    $this->withHandler('!', true, function($tag, $state) {
      $state->target->add(new CommentNode(trim(substr($tag, 1), ' ')));
    });

    // Change start and end
    $this->withHandler('=', true, function($tag, $state) {
      list($state->start, $state->end)= explode(' ', trim(substr($tag, 1, -1)));
    });

    // & for unescaped
    $this->withHandler('&', false, function($tag, $state, $parse) {
      $parsed= $parse->options(trim(substr($tag, 1)));
      $state->target->add('.' === $parsed[0]
        ? new IteratorNode(false)
        : new VariableNode($parsed[0], false, array_slice($parsed, 1))
      );
    });

    // triple mustache for unescaped
    $this->withHandler('{', false, function($tag, $state, $parse) {
      $parsed= $parse->options(trim(substr($tag, 1)));
      $state->target->add('.' === $parsed[0]
        ? new IteratorNode(false)
        : new VariableNode($parsed[0], false, array_slice($parsed, 1))
      );
      return +1; // Skip "}"
    });

    // Default
    $this->withHandler(null, false, function($tag, $state, $parse) {
      $parsed= $parse->options(trim($tag));
      $state->target->add('.' === $parsed[0]
        ? new IteratorNode(true)
        : new VariableNode($parsed[0], true, array_slice($parsed, 1))
      );
    });
  }
}