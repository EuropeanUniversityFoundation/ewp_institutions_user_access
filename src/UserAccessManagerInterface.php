<?php

declare(strict_types=1);

namespace Drupal\ewp_institutions_user_access;

use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines an interface for a user access manager service.
 */
interface UserAccessManagerInterface {

  const OPERATION_ADD = 'add';
  const OPERATION_VIEW = 'view';
  const OPERATION_EDIT = 'update';
  const OPERATION_DELETE = 'delete';
  const OPERATION_OTHER = 'other';

  /**
   * Calculates access to an entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to check access to.
   * @param string $operation
   *   The operation that is to be performed on $entity.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account trying to access the entity.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   *
   * @see hook_entity_access()
   */
  public function calculateAccess(EntityInterface $entity, string $operation, AccountInterface $account): AccessResultInterface;

  /**
   * Retrieves all user access restrictions that may apply to the entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to check.
   *
   * @return array
   *   A list of user access restrictions.
   */
  public function getEntityRestrictions(EntityInterface $entity): array;

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
  public function getSortedTargetId(EntityInterface $entity, string $field): array;

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
  public function referenceValuesMatch(array $user_ref, array $ref, bool $match_all = FALSE): bool;

}
