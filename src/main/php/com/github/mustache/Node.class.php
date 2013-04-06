<?php
  namespace com\github\mustache;

  abstract class Node extends \lang\Object {

  	public abstract function evaluate($context);

  	public static function parse($template) {
      $parsed= new NodeList();
      $parents= array();
      $st= new \text\StringTokenizer($template, '{');
      while ($st->hasMoreTokens()) {

        // Text
        $text= $st->nextToken();
        $parsed->add(new TextNode($text));
        if (!$st->hasMoreTokens()) break;

        // Found a tag
        $st->nextToken('{');
        $tag= $st->nextToken('}');
        $st->nextToken('}');

        if ('#' === $tag{0}) {          // start section
          $name= substr($tag, 1);
          $parents[$name]= $parsed;
          $parsed= $parsed->add(new SectionNode($name));
          $st->nextToken("\n");
        } else if ('/' === $tag{0}) {   // end section
          $name= substr($tag, 1);
          $parsed= $parents[$name];
          $st->nextToken("\n");
        } else if ('&' === $tag{0}) {   // & for unescaped
          $parsed->add(new VariableNode(ltrim(substr($tag, 1), ' '), FALSE));
        } else if ('{' === $tag{0}) {   // triple mustache for unescaped
          $parsed->add(new VariableNode(substr($tag, 1), FALSE));
          $st->nextToken('}');
        } else {
          $parsed->add(new VariableNode($tag));
        }
      }

      // \util\cmd\Console::writeLine($parsed);
      return $parsed;
    }
  }
?>