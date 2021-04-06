<?php

namespace Drupal\ww_drupal_core\Plugin\GraphQL\DataProducer;

use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\graphql\Plugin\DataProducerPluginCachingInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;

/**
 * @DataProducer(
 *   id = "field_entity",
 *   name = @Translation("Field Entity"),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Entity")
 *   ),
 *   consumes = {
 *     "field" = @ContextDefinition("any",
 *       label = @Translation("Field")
 *     )
 *   }
 * )
 */
class FieldEntity extends DataProducerPluginBase implements DataProducerPluginCachingInterface {

  public function resolve(EntityReferenceItem $field): ?string {
    if ($field->isEmpty()) {
      return NULL;
    }
    return $field[0]->entity;
  }

}
