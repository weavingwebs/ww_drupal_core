<?php

namespace Drupal\ww_drupal_core\Plugin\GraphQL\DataProducer;

use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\graphql\Plugin\DataProducerPluginCachingInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\link\LinkItemInterface;

/**
 * @DataProducer(
 *   id = "field_link_uri",
 *   name = @Translation("Link Uri"),
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
class FieldLinkUri extends DataProducerPluginBase implements DataProducerPluginCachingInterface {

  public function resolve(FieldItemListInterface $field, RefinableCacheableDependencyInterface $metadata): ?string {
    if ($field->isEmpty()) {
      return NULL;
    }
    $item = $field->first();
    assert($item instanceof LinkItemInterface);
    $url = $item->getUrl()->toString(TRUE);
    $metadata->addCacheableDependency($url);
    return $url->getGeneratedUrl();
  }

}
