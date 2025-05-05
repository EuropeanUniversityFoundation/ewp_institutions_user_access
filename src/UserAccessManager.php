<?php

declare(strict_types=1);

namespace Drupal\ewp_institutions_user_access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;

/**
 * User access manager service.
 */
final class UserAccessManager implements UserAccessManagerInterface {

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
   * Constructs an UserAccessManager object.
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
  public function calculate(EntityInterface $entity, string $operation, AccountInterface $account): AccessResultInterface {
    $restrictions = $this->getApplicableRestrictions($entity);

    // If there are no restrictions, the process ends here.
    if (empty($restrictions)) {
      // return AccessResult::neutral();
    }

    // Permissions on own content are more specific and should take precedence.
    if ($entity->hasField('uid')) {
      $author_id = $entity->get('uid')->getValue()[0]['target_id'] ?? NULL;

      if ($author_id === $account->id()) {
        dpm('User is the author / owner of the entity.');
        // return AccessResult::neutral();
      }
    }

    $user = User::load($account->id());
    $user_field_value = $this->getSortedTargetId($user, 'user_institution');

    foreach ($restrictions as $restriction) {
      $reference_field = $restriction->getReferenceFieldName();
      $field_value = $this->getSortedTargetId($entity, $reference_field);

      // Reference field value is necessary for the restriction.
      if (!empty($field_value)) {
        // TODO: pad this with usefulness!
      }
    }

    return AccessResult::neutral();
  }

  /**
   * Retrieves all user access restrictions that may apply to the entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to check.
   *
   * @return array
   *   A list of user access restrictions.
   */
  private function getApplicableRestrictions(EntityInterface $entity): array {
    $restrictions = $this->entityTypeManager
      ->getStorage('user_access_restriction')
      ->loadByProperties([
        'restricted_type' => $entity->getEntityTypeId(),
        'restricted_bundle' => $entity->bundle(),
      ]);

    return $restrictions;
  }

  /**
   * Provides a sorted list of referenced entity IDs.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to check.
   * @param string $field
   *   The field name to check.
   *
   * @return array
   *   A sorted list of referenced entity IDs.
   */
  private function getSortedTargetId(EntityInterface $entity, string $field): array {
    $field_value = $entity->get($field)->getValue();

    $target_id = [];

    foreach ($field_value as $item) {
      if (!empty($item)) {
        $target_id[] = $item['target_id'];
      }
    }

    asort($target_id);

    return $target_id;
  }

}
