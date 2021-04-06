<?php

namespace Drupal\ww_drupal_core\Plugin\GraphQL\DataProducer;

use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;

/**
 * @DataProducer(
 *   id = "try_seek",
 *   name = @Translation("Seek without dying if NULL"),
 *   description = @Translation("Seeks an array position."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Element")
 *   ),
 *   consumes = {
 *     "input" = @ContextDefinition("any",
 *       label = @Translation("Input array"),
 *       required = FALSE
 *     ),
 *     "position" = @ContextDefinition("integer",
 *       label = @Translation("Seek position")
 *     )
 *   }
 * )
 */
class TrySeek extends DataProducerPluginBase {

  public function resolve(?array $input, int $position) {
    if ($input === NULL) {
      return NULL;
    }
    $array_object = new \ArrayObject($input);
    $iterator = $array_object->getIterator();
    try {
      $iterator->seek($position);
    }
    catch (\OutOfBoundsException $e) {
      return NULL;
    }
    return $iterator->current();
  }

}
