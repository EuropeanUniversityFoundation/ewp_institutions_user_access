<?php

declare(strict_types=1);

namespace Drupal\ewp_institutions_user_access;

use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ewp_institutions_user_access\Entity\UserAccessRestriction;

/**
 * Service class providing options for user access restriction entities.
 */
final class UserAccessRestrictionOptions implements UserAccessRestrictionOptionsInterface {

  const SEPARATOR = UserAccessRestrictionOptionsInterface::SEPARATOR;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The entity type bundle info.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs an UserAccessRestrictionOptions object.
   *
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entityFieldManager
   *   The entity field manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entityTypeBundleInfo
   *   The entity type bundle info.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
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
      // Consider only content entities.
      if ($entity_definition instanceof ContentEntityType) {
        $entity_type = $entity_definition->id();
        $bundles = $this->entityTypeBundleInfo->getBundleInfo($entity_type);

        foreach ($bundles as $bundle_id => $bundle_info) {
          $field_definitions = $this->entityFieldManager
            ->getFieldDefinitions($entity_type, $bundle_id);

          foreach ($field_definitions as $field) {
            // Consider only entity reference fields targetting Institutions.
            if ($field->getType() === 'entity_reference') {
              $target_type = $field->getSettings()['target_type'];

              if ($target_type === 'hei') {
                $key = implode(self::SEPARATOR, [
                  $entity_type,
                  $bundle_id,
                  $field->getName(),
                ]);

                // Avoid providing options for machine names already in use.
                if (!UserAccessRestriction::load($key)) {
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
    }

    ksort($options);

    return $options;
  }

}
