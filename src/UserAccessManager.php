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
use Drupal\user\EntityOwnerInterface;
use Drupal\ewp_institutions_user\InstitutionUserBridge;
use Drupal\ewp_institutions_user_access\Entity\UserAccessRestrictionInterface;

/**
 * User access manager service.
 */
final class UserAccessManager implements UserAccessManagerInterface {

  const BASE_FIELD = InstitutionUserBridge::BASE_FIELD;

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
      return AccessResult::neutral()->addCacheableDependency($entity);
    }

    // When the user is the owner of the entity, defer to specific permissions.
    if ($entity instanceof EntityOwnerInterface) {
      if ($account->id() === $entity->getOwnerId()) {
        return AccessResult::neutral()
          ->cachePerPermissions()
          ->addCacheableDependency($entity);
      }
    }

    $user = $this->entityTypeManager->getStorage('user')->load($account->id());
    $user_ref = $this->getSortedTargetId($user, self::BASE_FIELD);

    $forbidden = FALSE;

    foreach ($restrictions as $restriction) {
      if (!$forbidden) {
        $reference_field = $restriction->getReferenceFieldName();
        $ref = $this->getSortedTargetId($entity, $reference_field);

        // Reference field value is necessary to calculate the restriction.
        if (!empty($ref)) {
          switch ($operation) {
            case self::OPERATION_VIEW:
              $match_all = $restriction->getRestrictViewMatchAll();
              $match = $this->valuesMatch($user_ref, $ref, $match_all);
              $forbidden = (!$match && $restriction->getRestrictView());
              break;

            case self::OPERATION_EDIT:
              $match_all = $restriction->getRestrictEditMatchAll();
              $match = $this->valuesMatch($user_ref, $ref, $match_all);
              $forbidden = (!$match && $restriction->getRestrictEdit());
              break;

            case self::OPERATION_DELETE:
              $match_all = $restriction->getRestrictDeleteMatchAll();
              $match = $this->valuesMatch($user_ref, $ref, $match_all);
              $forbidden = (!$match && $restriction->getRestrictDelete());
              break;

            default:
              $match_all = $restriction->getRestrictOtherMatchAll();
              $match = $this->valuesMatch($user_ref, $ref, $match_all);
              $forbidden = (!$match && $restriction->getRestrictOther());
              break;
          }
        }
      }
    }

    if ($forbidden) {
      return $this->accessForbidden($entity, $restriction);
    }

    $access = AccessResult::neutral()
      ->cachePerUser()
      ->addCacheableDependency($entity);

    return $access;
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
        'status' => TRUE,
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
    /** @var \Drupal\Core\Entity\FieldableEntityInterface $entity */
    $ref = $entity->get($field)->getValue();

    $target_id = [];

    foreach ($ref as $item) {
      if (!empty($item)) {
        $target_id[] = $item['target_id'];
      }
    }

    sort($target_id);

    return $target_id;
  }

  /**
   * Checks whether user field values match reference field values.
   *
   * @param array $user_ref
   *   User field values.
   * @param array $ref
   *   Reference field values.
   * @param bool $match_all
   *   Whether the values will be strictly matched.
   *
   * @return bool
   *   Indicates whether values match.
   */
  private function valuesMatch(array $user_ref, array $ref, bool $match_all = FALSE): bool {
    $overlap = array_values(array_intersect($user_ref, $ref));

    if ($match_all) {
      return ($overlap === $ref);
    }

    return !empty($overlap);
  }

  /**
   * Generates a forbidden AccessResult with appropriate caching.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to which access is being forbidden.
   * @param \Drupal\ewp_institutions_user_access\Entity\UserAccessRestrictionInterface $restriction
   *   The restriction that determines this access result.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  private function accessForbidden(EntityInterface $entity, UserAccessRestrictionInterface $restriction): AccessResultInterface {
    $access = AccessResult::forbidden()
      ->cachePerUser()
      ->addCacheableDependency($entity)
      ->addCacheableDependency($restriction);

    return $access;
  }

}
