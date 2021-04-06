<?php

namespace Drupal\ww_drupal_core\Plugin\GraphQL\DataProducer;

use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\Renderer;
use Drupal\graphql\Plugin\DataProducerPluginCachingInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DataProducer(
 *   id = "field_text",
 *   name = @Translation("Field Text"),
 *   description = @Translation("Returns a formatted string for a text field"),
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
class FieldText extends DataProducerPluginBase implements DataProducerPluginCachingInterface, ContainerFactoryPluginInterface {

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
    $build = [
      '#type' => 'processed_text',
      '#text' => $field_items[0]->value,
      '#format' => $field_items[0]->format,
    ];
    $context = new RenderContext();
    $result = $this->renderer->executeInRenderContext(
      $context,
      fn() => $this->renderer->renderPlain($build),
    );
    if (!$context->isEmpty()) {
      $metadata->addCacheableDependency($context->pop());
    }
    return $result;
  }
}
