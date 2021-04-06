<?php

namespace Drupal\ww_drupal_core\Plugin\GraphQL\DataProducer;

use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Renderer;
use Drupal\file\Entity\File;
use Drupal\graphql\Plugin\DataProducerPluginCachingInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DataProducer(
 *   id = "field_image",
 *   name = @Translation("Field Image"),
 *   description = @Translation("Returns an image url"),
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
class FieldImage extends DataProducerPluginBase implements DataProducerPluginCachingInterface, ContainerFactoryPluginInterface {

  protected Renderer $renderer;

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  public function resolve(FieldItemListInterface $field_items, RefinableCacheableDependencyInterface $metadata): ?string {
    if ($field_items->isEmpty()) {
      return NULL;
    }
    $image = $field_items[0]->entity;
    assert($image instanceof File);
    return file_create_url($image->getFileUri()) ?: NULL;
  }
}
