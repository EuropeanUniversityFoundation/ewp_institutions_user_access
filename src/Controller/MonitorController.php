<?php

declare(strict_types=1);

namespace Drupal\ewp_institutions_user_access\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\ContentEntityType;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for EWP Institutions User Access routes.
 */
final class MonitorController extends ControllerBase {

  const HEI = 'hei';

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
   * The controller constructor.
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    EntityTypeBundleInfoInterface $entityTypeBundleInfo,
    EntityFieldManagerInterface $entityFieldManager,
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->entityTypeBundleInfo = $entityTypeBundleInfo;
    $this->entityFieldManager = $entityFieldManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('entity_type.manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('entity_field.manager'),
    );
  }

  /**
   * Builds the response.
   */
  public function __invoke(): array {
    $header = [
      $this->t('Entity type'),
      $this->t('Entity bundle'),
      $this->t('Field name'),
      $this->t('Handler'),
    ];

    $rows = [];

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

              if ($target_type === self::HEI) {
                $rows[] = [
                  $entity_definition->getLabel(),
                  ($entity_type !== $bundle_id) ? $bundle_info['label'] : '',
                  $field->getLabel() . ' (' . $field->getName() . ')',
                  $field->getSettings()['handler'],
                ];
              }
            }
          }
        }
      }
    }

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('All fields referencing Institutions shown below.'),
    ];

    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('Nothing to display.'),
    ];

    return $build;
  }

}
