<?php

namespace Drupal\ww_drupal_core\Plugin\GraphQL\DataProducer;

use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\Renderer;
use Drupal\graphql\Plugin\DataProducerPluginCachingInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\metatag\MetatagManagerInterface;
use Drupal\schema_metatag\SchemaMetatagManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DataProducer(
 *   id = "metatags",
 *   name = @Translation("Metatags"),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Metatags")
 *   ),
 *   consumes = {
 *     "entity" = @ContextDefinition("any",
 *       label = @Translation("Entity")
 *     )
 *   }
 * )
 */
class Metatags extends DataProducerPluginBase implements DataProducerPluginCachingInterface, ContainerFactoryPluginInterface {

  protected MetatagManagerInterface $metatagManager;
  protected Renderer $renderer;
  protected ModuleHandlerInterface $moduleHandler;

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
    $instance->renderer = $container->get('renderer');
    $instance->metatagManager = $container->get('metatag.manager');
    $instance->moduleHandler = $container->get('module_handler');
    return $instance;
  }

  public function resolve(ContentEntityInterface $entity, RefinableCacheableDependencyInterface $metadata): array {
    $context = new RenderContext();
    $elements = $this->renderer->executeInRenderContext($context, function () use ($entity) {
      $tags = $this->metatagManager->tagsFromEntityWithDefaults($entity);
      $context = ['entity' => $entity];
      $this->moduleHandler->alter('metatags', $tags, $context);
      $result = $this->metatagManager->generateRawElements($tags, $entity);

      // Pull out the schema metatags because they have their own rendering step
      // as per \Drupal\schema_metatag\SchemaMetatagManager::getRenderedJsonld.
      $schema_elements = [];
      $elements = [];
      foreach ($result as $name => $tag) {
        if ($tag['#attributes']['schema_metatag'] ?? FALSE) {
          // Mimic \Drupal\metatag\MetatagManager::generateElements().
          $schema_elements[] = [
            $tag,
            $name,
          ];
        }
        else {
          $elements[$name] = $tag;

          // Use the title metatag (if set) as the <title>.
          if ($name === 'title') {
            $elements['title_tag'] = [
              '#tag' => 'title',
              '#value' => $tag['#attributes']['content'],
            ];
          }
        }
      }

      // Parse the Schema.org metatags out of the array.
      if ($items = SchemaMetatagManager::parseJsonld($schema_elements)) {
        // Encode the Schema.org metatags as JSON LD.
        if ($jsonld = SchemaMetatagManager::encodeJsonld($items)) {
          // Pass back the rendered result.
          $elements['schema_metatags'] = SchemaMetatagManager::renderArrayJsonLd($jsonld);
        }
      }

      return $elements;
    });

    if (!$context->isEmpty()) {
      $metadata->addCacheableDependency($context->pop());
    }
    $metadata->addCacheTags([
      'config:metatag.metatag_defaults.global',
      'config:metatag.metatag_defaults',// Not sure if this works?
      'config:metatag_defaults_list',
    ]);

    // Convert the render array.
    $tags = [];
    foreach ($elements as $key => $e) {
      $attributes = [];
      foreach ($e['#attributes'] ?? [] as $name => $value) {
        $attributes[] = [
          'name' => $name,
          'value' => $value,
        ];
      }

      $tags[] = [
        'id' => $key,
        'tag' => $e['#tag'],
        'children' => $e['#value'] ?? NULL,
        'attributes' => $attributes,
      ];
    }
    return $tags;
  }

}
