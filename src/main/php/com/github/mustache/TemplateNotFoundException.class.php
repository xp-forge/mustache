<?php namespace com\github\mustache;

/**
 * Indicates a template could not be found. Raised either directly
 * from MustacheEngine::transform() or during execution when loading
 * a partial via {{> partial}}.
 *
 * @see   xp://com.github.mustache.TemplateLoader#load
 */
class TemplateNotFoundException extends \lang\ElementNotFoundException {
}