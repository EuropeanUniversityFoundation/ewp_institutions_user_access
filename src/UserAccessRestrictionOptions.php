<?php

declare(strict_types=1);

namespace Drupal\ewp_institutions_user_access;

use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Service class providing options for user access restriction entities.
 */
final class UserAccessRestrictionOptions implements UserAccessRestrictionOptionsInterface {

  const SEPARATOR = UserAccessRestrictionOptionsInterface::SEPARATOR;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity type bundle info.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Constructs an UserAccessRestrictionOptions object.
   */
  public function __construct(
    EntityFieldManagerInterface $entityFieldManager,
    EntityTypeBundleInfoInterface $entityTypeBundleInfo,
    EntityTypeManagerInterface $entityTypeManager,
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->entityTypeBundleInfo = $entityTypeBundleInfo;
    $this->entityFieldManager = $entityFieldManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions(): array {
    $options = [];

    foreach ($this->entityTypeManager->getDefinitions() as $entity_definition) {
      if ($entity_definition instanceof ContentEntityType) {
        $entity_type = $entity_definition->id();
        $bundles = $this->entityTypeBundleInfo->getBundleInfo($entity_type);

        foreach ($bundles as $bundle_id => $bundle_info) {
          $field_definitions = $this->entityFieldManager
            ->getFieldDefinitions($entity_type, $bundle_id);

          foreach ($field_definitions as $field) {
            if ($field->getType() === 'entity_reference') {
              $target_type = $field->getSettings()['target_type'];

              if ($target_type === 'hei') {
                $key = implode(self::SEPARATOR, [
                  $entity_type,
                  $bundle_id,
                  $field->getName(),
                ]);

                $label_components = [$entity_definition->getLabel()];
                if ($entity_type !== $bundle_id) {
                  $label_components[] = $bundle_info['label'];
                }
                $label_components[] = $field->getLabel();

                $label = implode(': ', $label_components);

                $options[$key] = $label;
              }
            }
          }
        }
      }
    }

    ksort($options);

    return $options;
  }

}
