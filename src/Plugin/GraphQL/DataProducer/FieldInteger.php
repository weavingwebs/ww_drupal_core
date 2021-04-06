<?php

namespace Drupal\ww_drupal_core\Plugin\GraphQL\DataProducer;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\graphql\Plugin\DataProducerPluginCachingInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;

/**
 * @DataProducer(
 *   id = "field_integer",
 *   name = @Translation("Field String"),
 *   description = @Translation("Returns a integer for a given integer field"),
 *   produces = @ContextDefinition("integer",
 *     label = @Translation("String")
 *   ),
 *   consumes = {
 *     "field" = @ContextDefinition("any",
 *       label = @Translation("Field")
 *     )
 *   }
 * )
 */
class FieldInteger extends DataProducerPluginBase implements DataProducerPluginCachingInterface {

  public function resolve(FieldItemListInterface $field): ?int {
    if ($field->isEmpty()) {
      return NULL;
    }
    return $field[0]->value;
  }

}
