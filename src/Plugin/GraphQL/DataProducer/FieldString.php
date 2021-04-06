<?php

namespace Drupal\ww_drupal_core\Plugin\GraphQL\DataProducer;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\graphql\Plugin\DataProducerPluginCachingInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;

/**
 * @DataProducer(
 *   id = "field_string",
 *   name = @Translation("Field String"),
 *   description = @Translation("Returns a string for a given string field"),
 *   produces = @ContextDefinition("string",
 *     label = @Translation("String")
 *   ),
 *   consumes = {
 *     "field" = @ContextDefinition("any",
 *       label = @Translation("Field")
 *     )
 *   }
 * )
 */
class FieldString extends DataProducerPluginBase implements DataProducerPluginCachingInterface {

  public function resolve(FieldItemListInterface $field): ?string {
    if ($field->isEmpty()) {
      return NULL;
    }
    return $field[0]->value;
  }

}
