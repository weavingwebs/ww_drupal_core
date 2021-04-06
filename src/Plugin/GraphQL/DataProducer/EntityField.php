<?php

namespace Drupal\ww_drupal_core\Plugin\GraphQL\DataProducer;

use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\graphql\Plugin\DataProducerPluginCachingInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;

/**
 * @DataProducer(
 *   id = "entity_field",
 *   name = @Translation("Entity Field"),
 *   description = @Translation("Returns a field on an entity"),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Field")
 *   ),
 *   consumes = {
 *     "entity" = @ContextDefinition("entity",
 *       label = @Translation("Entity")
 *     ),
 *     "field" = @ContextDefinition("string",
 *       label = @Translation("Field Name")
 *     ),
 *     "access_check" = @ContextDefinition("boolean",
 *       label = @Translation("Access check"),
 *       required = FALSE,
 *     ),
 *   }
 * )
 */
class EntityField extends DataProducerPluginBase implements DataProducerPluginCachingInterface {

  public function resolve(FieldableEntityInterface $entity, string $field, ?bool $access_check, RefinableCacheableDependencyInterface $metadata): ?FieldItemListInterface {
    $field_items = $entity->get($field);
    assert($field_items instanceof FieldItemListInterface);
    if ($access_check) {
      $access = $field_items->access('view', NULL, TRUE);
      $metadata->addCacheableDependency($access);
      if (!$access->isAllowed()) {
        return NULL;
      }
    }
    return $field_items;
  }

}
