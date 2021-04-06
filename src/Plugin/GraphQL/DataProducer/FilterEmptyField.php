<?php

namespace Drupal\ww_drupal_core\Plugin\GraphQL\DataProducer;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\graphql\Plugin\DataProducerPluginCachingInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;

/**
 * @DataProducer(
 *   id = "filter_empty_field",
 *   name = @Translation("Filter empty field"),
 *   description = @Translation("Return null if a field on the entity is empty"),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Entity")
 *   ),
 *   consumes = {
 *     "entity" = @ContextDefinition("entity",
 *       label = @Translation("Entity")
 *     ),
 *     "field" = @ContextDefinition("string",
 *       label = @Translation("Field Name")
 *     )
 *   }
 * )
 */
class FilterEmptyField extends DataProducerPluginBase implements DataProducerPluginCachingInterface {

  /**
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   * @param string $field
   *
   * @return \Drupal\Core\Entity\FieldableEntityInterface|null
   */
  public function resolve(FieldableEntityInterface $entity, string $field): ?FieldableEntityInterface {
    return $entity->get($field)->isEmpty() ? NULL : $entity;
  }

}
