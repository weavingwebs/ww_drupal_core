<?php

namespace Drupal\ww_drupal_core\Plugin\GraphQL\DataProducer;

use Drupal\commerce_price\Plugin\Field\FieldType\PriceItem;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\graphql\Plugin\DataProducerPluginCachingInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;

/**
 * @DataProducer(
 *   id = "field_price",
 *   name = @Translation("Field Price"),
 *   description = @Translation("Returns a price for a given price field"),
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
class FieldPrice extends DataProducerPluginBase implements DataProducerPluginCachingInterface {

  public function resolve(FieldItemListInterface $field): ?array {
    if ($field->isEmpty()) {
      return NULL;
    }
    $item = $field->first();
    assert($item instanceof PriceItem);
    return $item->toPrice()->toArray();
  }

}
