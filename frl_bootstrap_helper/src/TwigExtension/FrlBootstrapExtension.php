<?php

namespace Drupal\frl_bootstrap_helper\TwigExtension;

use Drupal\Core\Template\Attribute;

/**
 * A test Twig extension that adds a custom function and a custom filter.
 */
class FrlBootstrapExtension extends \Twig_Extension {
/**
   * {@inheritdoc}
   */
  public function getName() {
    return 'add_attributes';
  }

  /**
   * Generates a list of all Twig functions that this extension defines.
   *
   * @return array
   *   A key/value array that defines custom Twig functions. The key denotes the
   *   function name used in the tag, e.g.:
   *   @code
   *   {{ testfunc() }}
   *   @endcode
   *
   *   The value is a standard PHP callback that defines what the function does.
   */
  public function getFunctions() {
    return [
      new \Twig_SimpleFunction(
        'add_attributes', 
        [$this, 'addAttributes'],
        array(
          'needs_context' => true,
          'is_safe' => array('html')
        ))
    ];
  }

  /**
   * Outputs either an uppercase or lowercase test phrase.
   *
   * The function generates either an uppercase or lowercase version of the
   * phrase "The quick brown fox jumps over the lazy dog 123.", depending on
   * whether or not the $upperCase parameter evaluates to TRUE. If $upperCase
   * evaluates to TRUE, the result will be uppercase, and if it evaluates to
   * FALSE, the result will be lowercase.
   *
   * @param bool $upperCase
   *   (optional) Whether the result is uppercase (true) or lowercase (false).
   *
   * @return string
   *   The generated string.
   *
   * @see \Drupal\system\Tests\Theme\TwigExtensionTest::testTwigExtensionFunction()
   */

  public static function addAttributes ($context, $additional_attributes = [], $attribute_type = 'attributes') {
      $attributes = new Attribute();
  
      $context_attribute = &$context;
      foreach(explode('.', $attribute_type) as $segment) {
        $context_attribute = &$context_attribute[$segment];
      }
  
      // if attribute doesn't exist, create it
      if (!$context_attribute) {
        $context_attribute = new Attribute();
      }
  
      if (!empty($additional_attributes)) {
        foreach ($additional_attributes as $key => $value) {
          if (is_array($value)) {
            foreach ($value as $index => $item) {
              if ($item instanceof Attribute) {
                // Remove the item.
                unset($value[$index]);
                $value = array_merge($value, $item->toArray()[$key]);
              }
            }
          }
          else {
            if ($value instanceof Attribute) {
              $value = $value->toArray()[$key];
            }
            elseif (is_string($value)) {
              $value = [$value];
            }
            else {
              continue;
            }
          }
  
          // Merge additional attribute values with existing ones.
          if ($context_attribute->offsetExists($key)) {
            $existing_attribute = $context_attribute->offsetGet($key)->value();
            $value = array_merge($existing_attribute, $value);
          }
  
          $context_attribute->setAttribute($key, $value);
        }
      }
  
      // Set all attributes.
      foreach($context_attribute as $key => $value) {
        $attributes->setAttribute($key, $value);
        // Remove this attribute from context so it doesn't filter down to child
        // elements.
        $context_attribute->removeAttribute($key);
      }
  
      return $attributes;
    
  
  }


}
