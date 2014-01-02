<?php namespace com\github\mustache;

/**
 * Parses mustache templates
 *
 * @test  xp://com.github.mustache.unittest.ParsingTest
 */
class MustacheParser extends AbstractMustacheParser {

  /**
   * Initialize this parser.
   */
  protected function initialize() {

    // Sections
    $this->withHandler('#^', true, function($tag, $state, $options) {
      $parsed= $options(trim(substr($tag, 1)));
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
    $this->withHandler('&', false, function($tag, $state, $options) {
      $parsed= $options(trim(substr($tag, 1)));
      $state->target->add('.' === $parsed[0]
        ? new IteratorNode(false)
        : new VariableNode($parsed[0], false, array_slice($parsed, 1))
      );
    });

    // triple mustache for unescaped
    $this->withHandler('{', false, function($tag, $state, $options) {
      $parsed= $options(trim(substr($tag, 1)));
      $state->target->add('.' === $parsed[0]
        ? new IteratorNode(false)
        : new VariableNode($parsed[0], false, array_slice($parsed, 1))
      );
      return +1; // Skip "}"
    });

    // Default
    $this->withHandler(null, false, function($tag, $state, $options) {
      $parsed= $options(trim($tag));
      $state->target->add('.' === $parsed[0]
        ? new IteratorNode(true)
        : new VariableNode($parsed[0], true, array_slice($parsed, 1))
      );
    });
  }
}