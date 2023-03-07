<?php

namespace Drupal\ww_drupal_core;

class WW {

  public static function createEntityTypes(array $entity_type_ids): void {
    foreach ($entity_type_ids as $entity_type_id) {
      $entity_type = \Drupal::entityTypeManager()->getDefinition($entity_type_id);
      if (!$entity_type) {
        throw new \RuntimeException('Entity type does not exist: ' . $entity_type_id);
      }
      \Drupal::entityDefinitionUpdateManager()->installEntityType($entity_type);
    }
  }

  public static function entityAddFields(string $entity_type_id, array $fields): void {
    $update_manager = \Drupal::entityDefinitionUpdateManager();
    \Drupal::entityTypeManager()->clearCachedDefinitions();
    foreach ($fields as $field_name => $field) {
      assert(is_string($field_name));
      $update_manager->installFieldStorageDefinition($field_name, $entity_type_id, $entity_type_id, $field);
    }
  }

  public static function eta($start_time, $progress, $max): string {
    $time = time() - $start_time;
    if ($time < 10) {
      return '[estimating]';
    }

    $time_left = ($time / $progress) * ($max - $progress);

    $tokens = [
      3600 => 'h',
      60 => 'm',
      1 => 's',
    ];

    $out = '';
    $remainder = $time_left;
    foreach ($tokens as $unit => $suffix) {
      if ($time_left < $unit) {
        continue;
      }
      $t = floor($remainder / $unit);
      $remainder -= ($t * $unit);
      $out .= $t . $suffix;
    }
    return $out;
  }
}
