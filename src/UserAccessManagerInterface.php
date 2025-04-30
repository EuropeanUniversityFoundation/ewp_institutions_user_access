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
  public function calculate(EntityInterface $entity, string $operation, AccountInterface $account): AccessResultInterface;

}
